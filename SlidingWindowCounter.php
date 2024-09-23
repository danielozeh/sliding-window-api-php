<?php

class SlidingWindowCounter
{
    private $windowSizeInSeconds;
    private $buckets;
    private $currentBucketIndex;
    private $totalEvents;
    private $lastTimestamp;

    public function __construct($windowSizeInSeconds)
    {
        $this->windowSizeInSeconds = $windowSizeInSeconds;
        $this->buckets = array_fill(0, $windowSizeInSeconds, 0);  // Initialize the circular buffer
        $this->currentBucketIndex = 0;
        $this->totalEvents = 0;
        $this->lastTimestamp = floor(microtime(true));  // Current time in seconds
    }

    // Record an event happening at the current time
    public function recordEvent()
    {
        $now = floor(microtime(true));
        $this->advanceTime($now);

        $this->buckets[$this->currentBucketIndex]++;
        $this->totalEvents++;
    }

    // Get the total number of events in the current window
    public function getEventCount()
    {
        $now = floor(microtime(true));
        $this->advanceTime($now);
        return $this->totalEvents;
    }

    // Advance the circular buffer based on the current time
    private function advanceTime($now)
    {
        $timePassed = $now - $this->lastTimestamp;
        if ($timePassed > 0) {
            for ($i = 0; $i < min($timePassed, $this->windowSizeInSeconds); $i++) {
                $this->currentBucketIndex = ($this->currentBucketIndex + 1) % $this->windowSizeInSeconds;
                $this->totalEvents -= $this->buckets[$this->currentBucketIndex]; // Remove expired events
                $this->buckets[$this->currentBucketIndex] = 0; // Clear the expired bucket
            }
            $this->lastTimestamp = $now;
        }
    }
}
