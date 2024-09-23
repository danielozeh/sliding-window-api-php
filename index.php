<?php

require_once 'SlidingWindowCounter.php';

// Set up the Sliding Window counter (300 seconds for 5 minutes)
$slidingWindow = new SlidingWindowCounter(300);

// Router logic
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

header('Content-Type: application/json');

switch ($requestUri) {
    case '/event':
        if ($requestMethod == 'POST') {
            $slidingWindow->recordEvent();
            echo json_encode(['message' => 'Event recorded']);
        } else {
            http_response_code(405);  // Method not allowed
            echo json_encode(['error' => 'Invalid request method']);
        }
        break;

    case '/events/count':
        if ($requestMethod == 'GET') {
            $eventCount = $slidingWindow->getEventCount();
            echo json_encode(['eventCount' => $eventCount]);
        } else {
            http_response_code(405);  // Method not allowed
            echo json_encode(['error' => 'Invalid request method']);
        }
        break;

    default:
        http_response_code(404);  // Not found
        echo json_encode(['error' => 'Route not found']);
        break;
}
