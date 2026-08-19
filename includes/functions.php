<?php
session_start();

function calculateScore($student_answers, $correct_answers, $total_questions) {
    $correct_count = 0;
    
    foreach ($correct_answers as $question_id => $correct_answer) {
        if (isset($student_answers[$question_id]) && $student_answers[$question_id] == $correct_answer) {
            $correct_count++;
        }
    }
    
    // Hitung score dengan maksimal 100
    if ($total_questions > 0) {
        $score = ($correct_count / $total_questions) * 100;
    } else {
        $score = 0;
    }
    
    return round($score);
}

function isTeacherLoggedIn() {
    return isset($_SESSION['teacher_logged_in']) && $_SESSION['teacher_logged_in'] === true;
}

function isStudentLoggedIn() {
    return isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true;
}

function getTotalQuestions($conn) {
    $sql = "SELECT COUNT(*) as total FROM quiz_questions";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

function shuffleAssoc($array) {
    $keys = array_keys($array);
    shuffle($keys);
    $shuffled = [];
    foreach ($keys as $key) {
        $shuffled[$key] = $array[$key];
    }
    return $shuffled;
}

function getQuestionStats($conn) {
    $sql = "SELECT 
            COUNT(*) as total_questions,
            (SELECT COUNT(*) FROM quiz_results) as total_attempts,
            (SELECT AVG(score) FROM quiz_results) as average_score
            FROM quiz_questions";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}
?>