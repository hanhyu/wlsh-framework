<?php

declare(strict_types=1);

namespace longlang\phpkafka\Client;

use InvalidArgumentException;
use longlang\phpkafka\Config\CommonConfig;
use longlang\phpkafka\Exception\UnsupportedApiKeyException;
use longlang\phpkafka\Exception\UnsupportedApiVersionException;
use longlang\phpkafka\Protocol\AbstractRequest;
use longlang\phpkafka\Protocol\AbstractResponse;
use longlang\phpkafka\Protocol\ApiKeys;
use longlang\phpkafka\Protocol\ApiVersions\ApiVersionsRequest;
use longlang\phpkafka\Protocol\ApiVersions\ApiVersionsResponse;
use longlang\phpkafka\Protocol\ErrorCode;
use longlang\phpkafka\Protocol\KafkaRequest;
use longlang\phpkafka\Protocol\RequestHeader\RequestHeader;
use longlang\phpkafka\Protocol\ResponseHeader\ResponseHeader;
use longlang\phpkafka\Protocol\Type\Int32;
use longlang\phpkafka\Socket\SocketInterface;
use longlang\phpkafka\Socket\StreamSocket;

class SyncClient implements ClientInterface
{
    /**
     * @var SocketInterface
     */
    protected $socket;

    /**
     * @var \longlang\phpkafka\Protocol\ApiVersions\ApiKeys[]
     */
    protected $apiKeys;

    /**
     * @var array
     */
    protected $waitResponseMaps;

    /**
     * @var int
     */
    protected $correlationIdIncrValue = 0;

    public function __construct(string $host, int $port, ?CommonConfig $config = null, string $socketClass = StreamSocket::class)
    {
        $this->socket = new $socketClass($host, $port, $config);
    }

    public function getHost(): string
    {
        return $this->socket->getHost();
    }

    public function getPort(): int
    {
        return $this->socket->getPort();
    }

    public function getConfig(): CommonConfig
    {
        return $this->socket->getConfig();
    }

    public function getSocket(): SocketInterface
    {
        return $this->socket;
    }

    /**
     * @param \longlang\phpkafka\Protocol\ApiVersions\ApiKeys[] $apiKeys
     */
    public function setApiKeys(array $apiKeys): ClientInterface
    {
        $result = [];
        foreach ($apiKeys as $item) {
            $result[$item->getApiKey()] = $item;
        }
        $this->apiKeys = $result;

        return $this;
    }

    /**
     * @return \longlang\phpkafka\Protocol\ApiVersions\ApiKeys[]|null
     */
    public function getApiKeys(): ?array
    {
        return $this->apiKeys;
    }

    public function connect(): void
    {
        $this->socket->connect();
        $this->waitResponseMaps = [];
        $this->updateApiVersions();
    }

    public function close(): bool
    {
        return $this->socket->close();
    }

    public function getRequestApiVersion(AbstractRequest $request): int
    {
        $apiKey = $request->getRequestApiKey();
        $apiKeyConfig = $this->apiKeys[$apiKey] ?? null;
        if (!$apiKeyConfig) {
            if (ApiKeys::PROTOCOL_API_VERSIONS === $apiKey) {
                return 1;
            }
            throw new UnsupportedApiKeyException(sprintf('Unsupported apikey %s', $apiKey));
        }
        $requestMaxSupportedVersion = $request->getMaxSupportedVersion();
        if ($apiKeyConfig->getMinVersion() > $request->getMaxSupportedVersion()) {
            throw new UnsupportedApiVersionException(sprintf('The version of Api %s must be >= v%s and <= v%s, the latest version currently supported is v%s', $request->getApiKeyText(), $apiKeyConfig->getMinVersion(), $apiKeyConfig->getMaxVersion(), $request->getMaxSupportedVersion()));
        }
        $apiKeyMaxVersion = $apiKeyConfig->getMaxVersion();
        if ($requestMaxSupportedVersion <= $apiKeyMaxVersion) {
            return $requestMaxSupportedVersion;
        } else {
            return $apiKeyMaxVersion;
        }
    }

    /**
     * Send message to kafka server.
     *
     * If successful, return the correlationId
     */
    public function send(AbstractRequest $request, ?RequestHeader $header = null, bool $hasResponse = true): int
    {
        $apiKey = $request->getRequestApiKey();
        if (null === $header) {
            $header = new RequestHeader();
            $header->setRequestApiKey($apiKey);
            $header->setRequestApiVersion($this->getRequestApiVersion($request));
            $header->setClientId($this->getConfig()->getClientId());
            $header->setCorrelationId(++$this->correlationIdIncrValue);
        }
        $kafkaRequest = new KafkaRequest($request, $header);
        $this->socket->send($kafkaRequest->pack());
        $correlationId = $header->getCorrelationId();

        if ($hasResponse) {
            $this->waitResponseMaps[$correlationId] = [
                'apiKey'           => $apiKey,
                'apiVersion'       => $header->getRequestApiVersion(),
                'flexibleVersions' => $request->getFlexibleVersions(),
            ];
        }

        return $correlationId;
    }

    public function recv(?int $correlationId, ?ResponseHeader &$header = null): AbstractResponse
    {
        if (!isset($this->waitResponseMaps[$correlationId])) {
            throw new InvalidArgumentException(sprintf('Invalid correlationId %s', $correlationId));
        }
        $mapData = $this->waitResponseMaps[$correlationId];
        $data = $this->socket->recv(4);
        $length = Int32::unpack($data);
        $data = $this->socket->recv($length);
        $header = new ResponseHeader();
        $header->unpack($data, $size, ResponseHeader::parseVersion($mapData['apiVersion'], $mapData['flexibleVersions']));
        $data = substr($data, $size);

        $result = ApiKeys::createResponse($mapData['apiKey'], $data, $mapData['apiVersion']);
        unset($this->waitResponseMaps[$correlationId]);

        return $result;
    }

    public function sendRecv(AbstractRequest $request, ?RequestHeader $requestHeader = null, ?ResponseHeader &$responseHeader = null): AbstractResponse
    {
        $correlationId = $this->send($request, $requestHeader);

        return $this->recv($correlationId, $responseHeader);
    }

    protected function updateApiVersions()
    {
        $request = new ApiVersionsRequest();
        $correlationId = $this->send($request);
        /** @var ApiVersionsResponse $response */
        $response = $this->recv($correlationId);
        ErrorCode::check($response->getErrorCode());
        $this->setApiKeys($response->getApiKeys());
    }
}
