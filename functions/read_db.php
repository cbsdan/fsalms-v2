<!--Week PANEL-->
<!-- Panels -->

<?php
function executeQuery($conn, $query) {
    // Attempt to execute the query
    $result = $conn->query($query);

    if ($result === TRUE) {
        // If query executed successfully
        return true;
    } elseif ($result->num_rows > 0) {
        // If the query fetched data (for SELECT queries)
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        // If there was an error or no results found
        return array("error" => $conn->error);
    }
}


function commandScalar($sql) {
    global $conn;
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['interest_rate']; // Replace 'column_name' with the appropriate column name from your query result
    } else {
        return null;
    }
}
?>

<!-- MEMBERS PANEL-->
<?php
//member's panel
function countMembers($conn) {

    try {
        $query = "SELECT COUNT(*) as total_members FROM members";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalMembers = (int) $row['total_members'];
        }

    } catch (Exception $ex) {
        echo "Error: " . $ex->getMessage();
        $totalMembers = 0;
    }
    return $totalMembers;
}

//Get male count
$queryMaleCount = "SELECT COUNT(mem_id) AS male_count FROM members WHERE sex = 'Male'";
$resultMaleCount = $conn->query($queryMaleCount);

if ($resultMaleCount && $resultMaleCount->num_rows > 0) {
    $rowMaleCount = $resultMaleCount->fetch_assoc();
    $maleCount = $rowMaleCount['male_count'];
    $lblMaleCount = (int) $maleCount;
} else {
    $lblMaleCount = 0;
}

// Get female count
$queryFemaleCount = "SELECT COUNT(mem_id) AS female_count FROM members WHERE sex = 'Female'";
$resultFemaleCount = $conn->query($queryFemaleCount);

if ($resultFemaleCount && $resultFemaleCount->num_rows > 0) {
    $rowFemaleCount = $resultFemaleCount->fetch_assoc();
    $femaleCount = $rowFemaleCount['female_count'];
    $lblFemaleCount = (int) $femaleCount;
} else {
    $lblFemaleCount = 0;
}
?>

<!--INTEREST SHARE PANEL-->

<?php
//function members
function getTotalInterest($conn) {
    $total_interest = 0;

    try {
        $sql = "SELECT SUM(ld.loan_amount * (ld.interest_rate / 100)) AS total_interest FROM loan_details ld INNER JOIN loan_requests lr ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Approved' AND lr.is_claim = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total_interest = (float)$row["total_interest"]; // Convert the result to float
        }
    } catch (Exception $ex) {
        // Handle exceptions
    }

    return $total_interest;
}


?>


<!-- LOAN PANEL-->

<?php
function getTotalLoan($conn) {
    $totalLoan = 0;

    try {
        // Get total loan amount with interest from the loan table
        $queryTotalLoan = "SELECT ROUND(SUM(ld.loan_amount + (ld.loan_amount * (ld.interest_rate / 100))), 2) AS total_loan FROM loan_details ld INNER JOIN loan_requests lr ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Approved' AND is_claim = 1;";
        $resultTotalLoan = $conn->query($queryTotalLoan);

        $totalLoan = 0;
        
        if ($resultTotalLoan->num_rows > 0) {
            $rowTotalLoan = $resultTotalLoan->fetch_assoc();
            $totalLoan = (float) $rowTotalLoan['total_loan'];
        }

    } catch (Exception $ex) {
        // Handle exceptions if needed
        echo "Error: " . $ex->getMessage();
    }

    return round($totalLoan, 2);
}
function getTotalUnpaidLoan($conn) {
    $totalUnpaidLoan = 0;

    try {
        $sql = "SELECT ROUND(SUM(payment_amount), 2) as total_amount FROM loan_payment";
        $result = $conn->query($sql);

        if ($result !== false && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $totalLoan = getTotalLoan($conn);
            $totalUnpaidLoan = (float) $totalLoan - $row["total_amount"] ;
            
        } else {
            $totalUnpaidLoan = 0;
        }
    } catch (Exception $ex) {
        echo $ex->getMessage(); // Display the exception message
        return 0;
    }

    return round($totalUnpaidLoan, 2);
}

function getTotalPaidLoan ($conn) {
    // Get total paid loan amount from loan_payment table
    $queryTotalPaidLoan = "SELECT SUM(payment_amount) AS total_paid_loan FROM loan_payment";
    $resultTotalPaidLoan = $conn->query($queryTotalPaidLoan);

    if ($resultTotalPaidLoan && $resultTotalPaidLoan->num_rows > 0) {
        $rowTotalPaidLoan = $resultTotalPaidLoan->fetch_assoc();
        $totalPaidLoan = (float) $rowTotalPaidLoan['total_paid_loan'];
        return number_format($totalPaidLoan, 2); // Format the total paid loan amount
    } else {
        return "0.00";
    }

}



?>

<!-- Calculating Dates -->

<?php
// Create DateTime objects from start and end dates
//$startDateTime = new DateTime($start_date);
//$endDateTime = new DateTime($end_date);

// Calculate the week number (assuming ISO-8601 week numbering)
//$weekTotal = (int) $startDateTime->format('W');

// Calculate the total weeks between start and end dates
//$interval = $endDateTime->diff($startDateTime);
//$weekNo= (int) ($interval->format('%a') / 7) + 1;

$lblWeekNo = 1;
$lblTotalWeeks = 12;

$currentDate = new DateTime();

// Calculate the start date based on the current date and week number
$startDateTime = clone $currentDate;
$startDateTime->modify('+' . ($lblWeekNo - 1) . ' weeks');

// Calculate the end date based on the start date and total number of weeks
$endDateTime = clone $startDateTime;
$endDateTime->modify('+' . ($lblTotalWeeks - 1) . ' weeks');

// Format dates for display
$startDate = $startDateTime->format('Y-m-d');
$endDate = $endDateTime->format('Y-m-d'); 
?>

<!-- MEMBERS SHARE PANEL -->

<?php

function getMembershipFee($conn) {
    $sql = "SELECT membership_fee FROM system_info LIMIT 1";
    $result = $conn -> query($sql);

    if ($result !== false && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['membership_fee'];
    } else {
        return 0;
    }
}

function getMemberSavings($conn) {
    $weekNo = (float) getWeekNumber($conn);
    $weekly_payment = (float) getWeeklyPayment($conn);

    $memberSavings =  (float) $weekNo * $weekly_payment;

    return $memberSavings;
}

function getMemberInterestsShare($conn) {
    $membersCount = countMembers($conn);

    if ($membersCount <= 0) {
        $memberInterestShare = 0;
    } else {
        $memberInterestShare = getTotalInterest($conn) * (getMemberPercentage($conn) / 100) / $membersCount;
    }
    return $memberInterestShare;
}

?>

<!-- INTEREST PANEL -->
<?php

function getTotalPaidInterests($conn) {
    $result = $conn->query("SELECT ROUND(SUM(ld.loan_amount * (ld.interest_rate / 100)), 2) AS paid_interest FROM loan_details ld INNER JOIN loan_requests lr ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 1;");
    $row = $result->fetch_assoc();
    if ($row !== null) {
        return round($row['paid_interest'], 2);
    } else {
        return 0;
    }

}

function getTotalPendingInterests($conn) {
    $result = $conn->query("SELECT ROUND(SUM(ld.loan_amount * (ld.interest_rate / 100)), 2) AS pending_interest FROM loan_details ld INNER JOIN loan_requests lr ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Approved' AND lr.is_claim = 1 AND ld.is_paid = 0;");
    $row = $result->fetch_assoc();
    if ($row !== null) {
        return round($row['pending_interest'], 2);
    } else {
        return 0;
    }

}

?>


<!-- SAVINGS PANEL -->
<?php
function getTotalAvailableMoney($conn) {
    $totalSavings = 0;
    $totalLoan = 0;
    $totalPaidLoan = 0;
    $availableMoney = 0;

    try {
        $result = $conn->query("SELECT SUM(deposited) FROM deposit;");
        if ($result !== false && $result->num_rows > 0) {
            $row = $result->fetch_row();
            $totalSavings = floatval($row[0]);
        }

        $result = $conn->query("SELECT SUM(ld.loan_amount) FROM loan_details ld INNER JOIN loan_requests lr ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Approved' AND is_claim = 1;");
        if ($result !== false && $result->num_rows > 0) {
            $row = $result->fetch_row();
            $totalLoan = floatval($row[0]);
        }

        $result = $conn->query("SELECT SUM(lp.payment_amount) FROM loan_payment lp INNER JOIN loan_details ld ON ld.loan_detail_id = lp.loan_detail_id;");
        if ($result !== false && $result->num_rows > 0) {
            $row = $result->fetch_row();
            $totalPaidLoan = floatval($row[0]);
        }

        $availableMoney = $totalSavings - $totalLoan + $totalPaidLoan;

    } catch (Exception $ex) {
        echo $ex->getMessage();
    }

    return number_format($availableMoney, 2);
}


function getTotalSavings($conn) {
    $totalSavings = 0;

    try {
        $result = $conn->query("SELECT SUM(deposited) FROM deposit;");
        if ($result !== false && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalSavings = isset($row['SUM(deposited)']) ? floatval($row['SUM(deposited)']) : 0;
        }

    } catch (Exception $ex) {
        echo $ex->getMessage();
    }

    return $totalSavings;
}

?>

<!-- USER-> info_section -->
<?php
function getTotalDeposits($conn, $mem_id) {
    $sql = "SELECT SUM(deposited) AS total_deposit FROM deposit WHERE mem_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mem_id); // Assuming mem_id is a string, change "s" to "i" if it's an integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the result
    $row = $result->fetch_assoc();
    $totalDeposit = $row['total_deposit'];

    // Return the total deposit
    return $totalDeposit;
}
?>

<?php

function getTotalWeeks($conn) {
    $start_date = getStartDate($conn);
    $end_date = getEndDate($conn);

    return abs(floor((new DateTime($start_date))->diff(new DateTime($end_date))->days / 7));

}


function getStartDate($conn) {
    // Fetch start_date from the system_info table
    $sql = "SELECT start_date FROM system_info";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Assuming you only have one row in the system_info table
        $row = $result->fetch_assoc();
        $start_date = $row['start_date'];

        return $start_date;
    } else {
        return "No rows found in the system_info table.";
    }
}

function getEndDate($conn) {
    // Fetch start_date from the system_info table
    $sql = "SELECT end_date FROM system_info";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Assuming you only have one row in the system_info table
        $row = $result->fetch_assoc();
        return $row['end_date'];

    } else {
        return "No rows found in the system_info table.";
    }
}


function getWeekNumber($conn) {
    // Fetch start_date from the system_info table
    $sql = "SELECT start_date FROM system_info";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Assuming you only have one row in the system_info table
        $row = $result->fetch_assoc();
        $start_date = $row['start_date'];

        // Get the current date
        $current_date = date("Y-m-d");

        // Calculate the number of seconds in a week
        $seconds_in_a_week = 60 * 60 * 24 * 7;

        // Calculate the difference in seconds between the start and current dates
        $start_timestamp = strtotime($start_date);
        $current_timestamp = strtotime($current_date);
        $difference = $current_timestamp - $start_timestamp;

        // Calculate the number of weeks
        $week_number = ceil($difference / $seconds_in_a_week);

        return ($week_number > 0) ? $week_number : 0;
    } else {
        return "No rows found in the system_info table.";
    }
}

?>
<?php
function getWeeklyPayment($conn) {
$sql = "SELECT weekly_payment FROM system_info";

// Execute the query
$result = $conn->query($sql);

// Check if the query was successful
if ($result) {
    // Fetch the result as an associative array
    $row = $result->fetch_assoc();

    // Close the database connection

    // Return the weekly payment value
    return (isset($row['weekly_payment']) ? $row['weekly_payment'] : 0);
} else {
    // If the query fails, return an error or handle it accordingly
    return "Error: " . $conn->error;
}
}
?>

<?php

// Function to compute the pending amount
function computePendingAmount($conn, $memId) {
    $totalDeposits = getTotalDeposits($conn, $memId) - 1000;
    $regularMemberSavings = getMemberSavings($conn);

    $pending = $regularMemberSavings - $totalDeposits;

    if ($pending < 0) {
        return 0; //this means that the member is depositing regularly
    } else {
        return $pending;
    }
}
?>
 <?php

function getMemberTotalPaidLoan ($conn, $mem_id) {
    // Get total paid loan amount from loan_payment table
    $queryTotalPaidLoan = "SELECT SUM(payment_amount) AS total_paid_loan FROM loan_payment WHERE mem_id = $mem_id";
    $resultTotalPaidLoan = $conn->query($queryTotalPaidLoan);

    if ($resultTotalPaidLoan && $resultTotalPaidLoan->num_rows > 0) {
        $rowTotalPaidLoan = $resultTotalPaidLoan->fetch_assoc();
        $totalPaidLoan = (float) $rowTotalPaidLoan['total_paid_loan'];
        return number_format($totalPaidLoan, 2); // Format the total paid loan amount
    } else {
        return "0.00";
    }

}
function getLoanBalance($conn, $member_id, $loan_detail_id) {
    $loanQuery = "
        SELECT ROUND(COALESCE(SUM(ld.loan_amount + (ld.loan_amount * (ld.interest_rate / 100))) - COALESCE((SELECT SUM(payment_amount) FROM loan_payment WHERE mem_id = $member_id AND loan_detail_id = $loan_detail_id), 0), 0), 2) AS loan_balance
        FROM loan_requests lr
        JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id
        WHERE lr.mem_id = $member_id
        AND ld.loan_detail_id = $loan_detail_id
        AND lr.request_status = 'Approved'
        AND lr.is_claim = 1
        AND ld.is_paid = 0;
    ";

    $loanResult = $conn->query($loanQuery);

    if ($loanResult->num_rows > 0) {
        // Calculate the total loan balance
        $loanResultArr = $loanResult->fetch_assoc();
        $loanBalance = $loanResultArr['loan_balance'];

        return $loanBalance;
    } else {
        // If the loan query fails, return an error or handle it accordingly
        return "Error: " . $conn->error;
    }
}
function getTotalLoanBalance($conn, $member_id) {
    $loanQuery = "
        SELECT ROUND(COALESCE(SUM(ld.loan_amount + (ld.loan_amount * (ld.interest_rate / 100))) - COALESCE((SELECT SUM(payment_amount) FROM loan_payment WHERE mem_id = $member_id), 0), 0), 2) AS loan_balance
        FROM loan_requests lr
        JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id
        WHERE lr.mem_id = $member_id
        AND lr.request_status = 'Approved'
        AND lr.is_claim = 1
    ";

    $loanResult = $conn->query($loanQuery);

    if ($loanResult->num_rows > 0) {
        // Calculate the total loan balance
        $loanResultArr = $loanResult->fetch_assoc();
        $totalLoanBalance = $loanResultArr['loan_balance'];

        return $totalLoanBalance;
    } else {
        // If the loan query fails, return an error or handle it accordingly
        return "Error: " . $conn->error;
    }
}
function getPendingLoanInterest($conn, $member_id) {
    $
    $totalLoan = getTotalPaidLoan($conn);
}
function getTotalInterests ($conn, $member_id) {
    $interestQuery = "
        SELECT ROUND(COALESCE(SUM(ld.loan_amount * (ld.interest_rate / 100)), 0), 2) AS totalInterest
        FROM loan_requests lr
        JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id
        WHERE lr.mem_id = $member_id
        AND lr.request_status = 'Approved'
        AND lr.is_claim = 1
        AND ld.is_paid = 0;
    ";

    $queryResult = $conn->query($interestQuery);

    if ($queryResult->num_rows > 0) {
        // Calculate the total loan balance
        $totalInterestArr = $queryResult->fetch_assoc();
        $totalInterests = $totalInterestArr['totalInterest'];

        return $totalInterests;
    } else {
        // If the loan query fails, return an error or handle it accordingly
        return 0;
    }
}
?>

<?php

function computeInterestShare($conn, $loanAmount, $member_id) {
    // Fetch loan details from loan_details table for the specified member_id
    $loanDetailsQuery = "
        SELECT lr.mem_id, lr.request_status, ld.loan_amount, ld.interest_rate, ld.month_duration
        FROM loan_requests lr
        JOIN loan_details ld ON lr.mem_id = ld.loan_detail_id
        WHERE lr.mem_id = $member_id
    ";

    $loanDetailsResult = $conn->query($loanDetailsQuery);

    if ($loanDetailsResult && $loanDetailsResult->num_rows > 0) {
        $loanDetails = $loanDetailsResult->fetch_assoc();

        // Extract loan details
        $requestStatus = $loanDetails['request_status'];
        $interestRate = $loanDetails['interest_rate'];
        $loanDurationMonths = $loanDetails['month_duration'];

        // Check if the loan request is approved
        if ($requestStatus == 'approved') {
            // Convert annual interest rate to weekly rate
            $weeklyInterestRate = ($interestRate / 100) / 52;

            // Calculate the total interest over the loan period using compound interest formula
            $compoundInterestFactor = pow(1 + $weeklyInterestRate, 52 * $loanDurationMonths / 12);
            $totalInterest = $loanAmount * $compoundInterestFactor - $loanAmount;
        
            // Calculate the interest share per week
            $interestSharePerWeek = $totalInterest / ($loanDurationMonths * 4); // Assuming 4 weeks in a month
        
            return number_format($interestSharePerWeek, 2);
        } else {
            // Handle the case where the loan request is not approved
            return "Error: Loan request not approved for member $member_id";
        }
    } else {
        // Handle the case where the loan details are not found
        return "Error: Loan details not found for member $member_id";
    }
}


function countLoanRequests($conn) {
    $sql = "SELECT COUNT(*) AS total_requests FROM members m INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id INNER JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Pending'";
    $result = $conn->query($sql);

    if ($result->num_rows <= 0) {
        return 0;
    }

    $totalRequests = $result->fetch_assoc();
    return $totalRequests['total_requests'];
}

function getManagerPercentage ($conn) {
    $sql = "SELECT manager_percentage FROM system_info si LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows <= 0) {
        return 0;
    }

    $managerPercent = $result->fetch_assoc();
    return $managerPercent['manager_percentage'];
}
function getMemberPercentage ($conn) {
    $sql = "SELECT member_percentage FROM system_info si LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows <= 0) {
        return 0;
    }

    $managerPercent = $result->fetch_assoc();
    return $managerPercent['member_percentage'];
}

function getLoanInterestRate ($conn, $transacId) {
    $sql = "SELECT interest_rate FROM loan_details WHERE loan_detail_id = $transacId";
    $result = $conn->query($sql);

    if ($result->num_rows <= 0) {
        return 0;
    }

    $interestRate = $result->fetch_assoc();
    return $interestRate['interest_rate'];
}
?>




