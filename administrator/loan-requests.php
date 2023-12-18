<?php
$authentication_path = '../functions/user-authenticate.php';
$authentication_path_index = './functions/user-authenticate.php';
if (file_exists($authentication_path)) {
    include_once("$authentication_path");
} elseif (file_exists($authentication_path_index)) {
    include_once("$authentication_path_index");
}

$database_path = '../database/config.php';
$database_path_index = './database/config.php';

if (file_exists($database_path)) {
    include_once($database_path);
} else {
    include_once($database_path_index);
}

$request_sql = "SELECT *, CONCAT(m.fname , ' ', m.lname) AS name FROM members m INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id INNER JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id WHERE lr.request_status = 'Pending' ORDER BY date_requested DESC";
$loan_requests = $conn->query($request_sql);

//initialize records_sql
$records_sql = "SELECT *, CONCAT(m.fname , ' ', m.lname) AS name FROM members m INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id INNER JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id WHERE (lr.request_status = 'Approved' OR lr.request_status = 'Declined') ORDER BY date_requested DESC";

if (isset($_SESSION['status'])) {
    $status = $_SESSION['status'];
    if ($status == 1) {
        $filterStatus = 'Approved';
    } else {
        $filterStatus = 'Declined';

    }
    $_SESSION['status'] = null;
}

//If admin want to filter record whether it is approved or declined, this will be the query:
if (isset($status)) {
    $records_sql = "SELECT *, CONCAT(m.fname , ' ', m.lname) AS name 
                    FROM members m 
                    INNER JOIN loan_requests lr ON lr.mem_id = m.mem_id 
                    INNER JOIN loan_details ld ON lr.loan_detail_id = ld.loan_detail_id 
                    WHERE lr.request_status = '$filterStatus'
                    ORDER BY date_requested DESC";
}
$records = $conn->query($records_sql);

?>
<h1>Loan Requests</h1>
<hr>
<div class="loan-requests">
<h4 class="mb-3">Available Money: ₱ <span class="value"><?php echo getTotalAvailableMoney($conn)?></span></h4>
<h4 class="mb-3">Total: <span class="value"><?php echo $loan_requests->num_rows?></span></h4>
<div class="result">
    <table class="result-table">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Name</th>
                <th>Member Type</th>
                <th>Amount</th>
                <th>Duration</th>
                <th>Interest Rate</th>
                <th>Claim Date</th>
                <th>Date Requested</th> 
                <th>Status</th> 
                <th>Approve</th>
                <th>Decline</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($loan_requests->num_rows > 0) {
                    while ($loan_request = $loan_requests->fetch_assoc()) {
                        if ($loan_request['is_temp_mem'] == 0 || $loan_request['is_temp_mem'] == false) { 
                            $member_status = 'Member';
                         } else {
                            $member_status = 'Temporary';
                         } 

                        echo " <tr>
                                <td>" . $loan_request ['request_id'] . "</td>
                                <td>" . $loan_request ['name'] . "</td>
                                <td class='text-center'>" . $member_status . "</td>
                                <td>₱" . $loan_request ['loan_amount'] . "</td>
                                <td class='text-center'>" . $loan_request ['month_duration'] . "</td>
                                <td class='text-center'>" . $loan_request ['interest_rate'] . "%</td>
                                <td>" . $loan_request ['claim_date'] . "</td>
                                <td>" . $loan_request ['date_requested'] . "</td>
                                <td class='text-center'>" . $loan_request ['request_status'] . "</td>
                                <td>
                                    <form action='./database/update-loan-request.php' method='POST'>
                                        <input type='hidden' name='request_id' value='" . $loan_request ['request_id'] . "'>
                                        <button type='submit' name='approve' value='approve' class='bg-green m-auto'>Approve</button>
                                    </form>
                                </td>
                                <td>
                                    <form action='./database/update-loan-request.php' method='POST'>
                                        <input type='hidden' name='request_id' value='" . $loan_request ['request_id'] . "'>
                                        <button type='submit' name='decline' value='decline' class='bg-red m-auto'>Decline</button>
                                    </form>
                                </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No Requests</td></tr>";
                }
            ?>
        </tbody>
    </table>
</div>
</div>
<h1>Records</h1>
<hr>
<div class="container" id="loan-requests-total-select">
    <h4>Total: <span class="value"><?php echo $records->num_rows?></span></h4>
    
    <form action="./database/filter-loan-requests.php" method="POST" id='statusForm'>
        <select name="filter-record" id="selectFilter">
            <option value="All" selected>All</option>
            <option value="Approved" <?php if (isset ($filterStatus) && $filterStatus == 'Approved') {echo "selected";}?>>Approved</option>
            <option value="Declined" <?php if (isset ($filterStatus) && $filterStatus == 'Declined') {echo "selected";}?>>Declined</option>
        </select>
    </form>
</div>
<div class="record result">
<table class="result-table">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Name</th>
                <th>Member Type</th>
                <th>Amount</th>
                <th>Duration</th>
                <th>Interest Rate</th>
                <th>Claim Date</th>
                <th>Date Requested</th> 
                <th>Status</th> 
                <th>Claim</th> 
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($records->num_rows > 0) {
                    while ($record = $records->fetch_assoc()) {
                        if ($record['is_claim'] == false && strtolower($record['request_status']) == 'approved') {
                            $claim_status = "
                                <form action='./database/update-loan-request.php' method='POST'>
                                    <input type='hidden' name='request_id' value='" . $record['request_id'] . "'>
                                    <button type='submit' name='claim' value='claim' class='m-auto'>Claim</button>
                                </form>";
                        } else if($record['is_claim'] == true && strtolower($record['request_status']) == 'approved') {
                            $claim_status = 'Claimed';
                        } else {
                            $claim_status = 'Unavailable';
                        }

                        if (strtolower($record['request_status']) == 'approved') {
                            $statusClass = 'c-green';
                        } else {
                            $statusClass = 'c-red';
                        }

                        if ($record['is_temp_mem'] == 0 || $record['is_temp_mem'] == false) { 
                            $member_status = 'Member';
                         } else {
                            $member_status = 'Temporary';
                         } 
                        echo "<tr>
                              <td>" .$record['request_id'] . "</td>
                              <td>" .$record['name'] . "</td>
                              <td class='text-center'>" . $member_status . "</td>
                              <td>₱" .$record['loan_amount'] . "</td>
                              <td class='text-center'>" .$record['month_duration'] . "</td>
                              <td class='text-center'>" .$record['interest_rate'] . "%</td>
                              <td>" .$record['claim_date'] . "</td>
                              <td>" .$record['date_requested'] . "</td>
                              <td class='$statusClass text-center'>" .$record['request_status'] . "</td>
                              <td class='text-center'>$claim_status</td>
                              <td>
                                <form action='./database/update-loan-request.php' method='POST' class='deleteLoanReq'>
                                    <input type='hidden' name='request_id' class='request_id' value='" . $record['request_id'] . "'>
                                    <button type='submit' name='delete' value='delete' class='bg-red m-auto deleteBtn'>Delete</button>
                                </form>
                              </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No Records</td></tr>";
                }
                
            ?>
        </tbody>
    </table>
</div>

<script>
    let deleteForms = document.querySelectorAll('.deleteLoanReq');
    deleteForms.forEach(deleteForm=>{
        let requestId = deleteForm.querySelector('.request_id');

        deleteForm.addEventListener('submit', (e)=>{
            let confrimDelete = confirm(`Do you want to delete loan requests with id of ${requestId.value}`);

            if (!confrimDelete) {
                e.preventDefault();
            }
        })
    })

    document.getElementById('selectFilter').addEventListener('change', ()=>{
        document.getElementById('statusForm').submit();
    });

    let imageContainers = document.querySelectorAll('td .imageContainer')
    imageContainers.forEach((imageContainer)=>{
        let altText = imageContainer.querySelector('.altText');
        let image = imageContainer.querySelector('img');

        imageContainer.addEventListener('click', ()=>{
            if (altText.classList.contains('hidden')) {
                altText.classList.remove('hidden');
                imageContainer.classList.remove('active');
                image.classList.add('hidden');
            } else {
                altText.classList.add('hidden');
                imageContainer.classList.add('active');
                image.classList.remove('hidden');
            }
        })
    })
</script>
