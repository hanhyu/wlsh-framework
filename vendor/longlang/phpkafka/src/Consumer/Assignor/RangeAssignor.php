<?php

declare(strict_types=1);

namespace longlang\phpkafka\Consumer\Assignor;

use longlang\phpkafka\Consumer\Struct\ConsumerGroupMemberMetadata;
use longlang\phpkafka\Group\Struct\ConsumerGroupMemberAssignment;
use longlang\phpkafka\Group\Struct\ConsumerGroupTopic;
use longlang\phpkafka\Protocol\JoinGroup\JoinGroupResponseMember;
use longlang\phpkafka\Protocol\Metadata\MetadataResponseTopic;
use longlang\phpkafka\Protocol\SyncGroup\SyncGroupRequestAssignment;

class RangeAssignor extends AbstractPartitionAssignor
{
    /**
     * @param MetadataResponseTopic[]   $topicMetadatas
     * @param JoinGroupResponseMember[] $groupMembers
     *
     * @return SyncGroupRequestAssignment[]
     */
    public function assign(array $topicMetadatas, array $groupMembers): array
    {
        $partitions = [];
        $memberTopics = [];
        foreach ($groupMembers as $groupMember) {
            $memberId = $groupMember->getMemberId();
            $consumerGroupMemberMetadata = new ConsumerGroupMemberMetadata();
            $consumerGroupMemberMetadata->unpack($groupMember->getMetadata());
            $memberTopics[$memberId] = $topics = $consumerGroupMemberMetadata->getTopics();
            foreach ($topics as $topic) {
                if (!isset($partitions[$topic])) {
                    $partitions[$topic] = $this->getTopicPartitions($topic, $topicMetadatas);
                    sort($partitions[$topic]);
                }
            }
        }
        /** @var SyncGroupRequestAssignment[] $assignments */
        $assignments = [];
        /** @var ConsumerGroupMemberAssignment[] $consumerGroupMemberAssignments */
        $consumerGroupMemberAssignments = [];
        foreach ($memberTopics as $memberId => $topics) {
            $assignments[] = $assignment = new SyncGroupRequestAssignment();
            $assignment->setMemberId($memberId);
            $consumerGroupMemberAssignments[] = new ConsumerGroupMemberAssignment();
        }

        $memberCount = \count($memberTopics);
        foreach ($partitions as $topicName => $topicPartitions) {
            $partitionCount = \count($topicPartitions);
            $numPartitionsPerConsumer = (int) ($partitionCount / $memberCount);
            $consumersWithExtraPartition = $partitionCount % $memberCount;

            for ($i = 0; $i < $memberCount; ++$i) {
                $start = $numPartitionsPerConsumer * $i + min($i, $consumersWithExtraPartition);
                $length = $numPartitionsPerConsumer + ($i + 1 > $consumersWithExtraPartition ? 0 : 1);

                $consumerGroupMemberAssignment = $consumerGroupMemberAssignments[$i];
                $consumerGroupTopic = new ConsumerGroupTopic();
                $consumerGroupTopic->setTopicName($topicName);
                $consumerGroupTopic->setPartitions(\array_slice($topicPartitions, $start, $length));
                $topics = $consumerGroupMemberAssignment->getTopics();
                $topics[] = $consumerGroupTopic;
                $consumerGroupMemberAssignment->setTopics($topics);
            }
        }

        foreach ($assignments as $i => $assignment) {
            $assignment->setAssignment($consumerGroupMemberAssignments[$i]->pack());
        }

        return $assignments;
    }
}
