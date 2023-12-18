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

if (isset($_GET['search'])) {
    $searchValue = $_GET['search-value'];
    $searchType = $_GET['search-type'];
    
    $member_status = $_GET['member_status'];

    if ($searchType == 'name') {
        //searchtype is name
        $sql = "SELECT *, members.mem_id, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.profile 
                FROM members
                LEFT JOIN accounts ON members.mem_id = accounts.mem_id 
                WHERE CONCAT(members.fname, ' ', members.lname) 
                LIKE '%$searchValue%' 
                AND members.is_temp_mem = 0
                AND ('$member_status' = 'all' OR members.status = '". ucfirst($member_status) ."')";

    } else {
        //searchtype is mem_id
        $sql = "SELECT *, members.mem_id, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.profile 
                FROM members
                LEFT JOIN accounts ON members.mem_id = accounts.mem_id 
                WHERE members.$searchType LIKE '%$searchValue%' 
                AND members.is_temp_mem = 0
                AND ('$member_status' = 'all' OR members.status = '". ucfirst($member_status) . "')";
    }
    $_SESSION['section'] = './administrator/savings-deposits.php';
    $_SESSION['activeNavId'] = 's-deposits';
    $_SESSION['sql_command'] = $sql;
    $_SESSION['selectedSearchType'] = $searchType;
    $_SESSION['searchValue'] = $searchValue;
    $_SESSION['member_status'] = $member_status;

    header('Location: ../administrator-ui.php');
    exit();
} 

//Default SQL Command
$sql = "SELECT *, members.mem_id, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.profile 
        FROM members
        LEFT JOIN accounts ON members.mem_id = accounts.mem_id
        WHERE members.is_temp_mem = 0";

//Search SQL Command
if (isset($_SESSION['sql_command']) && $_SESSION['selectedSearchType']) {
    $sql = $_SESSION['sql_command'];
    $searchType = $_SESSION['selectedSearchType'];
    $searchValue = $_SESSION['searchValue'];
    $member_status = $_SESSION['member_status'];
    $_SESSION['sql_command'] = null;
    $_SESSION['selectedSearchType'] = null;
    $_SESSION['searchValue'] = null;
    $_SESSION['member_status'] = null;
}
if (isset($_SESSION['mem_info'])) {
    $memInfo = $_SESSION['mem_info'];
    $_SESSION['mem_info'] = null;
}

//use to identify later if there is atleast a member fetch from database 

$isThereMember = false;
?>
<h1>Savings Deposit</h1>
<hr>    
<div class="savings-deposit">
    <div class="section first">
        <h3 class="title">1. Select a Member</h3>
        <div class="inner-section">
            <div class="left left-section">
                <form action="./administrator/savings-deposits.php" method="GET">
                    <div class="search-section">
                        <input class="search-input" type="text" class="search-input" placeholder="Search here" name="search-value" value="<?php if (isset($searchValue)) { echo $searchValue;}?>">
                        <select class="options select-input" name="search-type">
                            <option value="mem_id" class="option" <?php if (isset($searchType) && $searchType == 'mem_id') echo "selected"?>>ID</option>
                            <option value="name" class="option" <?php if (!isset($searchType) || $searchType == 'name') echo "selected"?>>Name</option>
                        </select> 
                        <select id='members-status-select' class="options select-input" name="member_status">
                            <option value="all" class="option" <?php if (!isset($member_status) || $member_status == 'all' ) echo "selected"?>>All</option>
                            <option value="regular" class="option" <?php if (isset($member_status) && $member_status == 'regular') echo "selected"?>>Regular</option>
                            <option value="irregular" class="option" <?php if (isset($member_status) && $member_status == 'irregular') echo "selected"?>>Irregular</option>
                        </select> 
                        <button type="submit" class="" id='search-btn' name="search" value="search"><img src='./img/search.png'></button>
                    </div>
                </form>
                <p class='mb-3'><span>Total Weekly Savings: ₱ </span><span class="value c-green"><?php echo number_format(getMemberSavings($conn) + getMembershipFee($conn), 2);?></span><span class='gray-text'> (+ ₱ <?php echo getMembershipFee($conn)?> Membership Fee)</span></p>
                <div class="result">
                    <table class="result-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Sex</th>
                                <th>Age</th> 
                                <th>Total Savings</th>
                                <th>Status</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $result = $conn->query($sql);
                            
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $memId = $row['mem_id'];

                                        $isSelected = false;

                                        if (isset($memInfo['mem_id'])) {
                                            if ($memId == $memInfo['mem_id']) {
                                                $isSelected = true;
                                            }
                                        }
                                        
                                        echo "<tr>";
                                        if (empty($row["profile"])) {
                                            echo "<td class='profile-img'><img src='./img/default-profile.png' alt='img'></td>";
                                        } else {
                                            $imgSrc = getImageSrc($row['profile']);
                                            echo "<td class='profile-img'><img src='$imgSrc' alt='img'></td>";
                                        }
                                        echo "<td>$memId</td>";
                                        echo "<td>" . $row['name']. "</td>";
                                        echo "<td>" . $row["sex"] . "</td>";
                                        echo "<td>" . $row['age']. "</td>";
                                        echo "<td class=". (((getMemberSavings($conn) + getMembershipFee($conn)) <= getTotalDeposits($conn, $memId)) ? 'c-green' : 'c-red') .">₱" . getTotalDeposits($conn, $memId) . "</td>";
                                        echo "<td class='text-center'>" . $row['status'] . "</td>";
                                        echo "<td>
                                                <form action='database/fetch_member_info.php' method='POST'>
                                                    <input type='hidden' name='mem_id' value='$memId'>
                                                    <input type='hidden' name='page' value='./administrator/savings-deposits.php'>
                                                    <input type='hidden' name='activeNavId' value='s-deposits'>
                                                    <button type='submit' name='select' value='select' class='m-auto select-btn ". ($isSelected ? "c-gold" : '') ."' " . ($isSelected ? "disabled" : '') . ">" . 
                                                        ($isSelected ? "Selected" : 'Select') . 
                                                    "</button>
                                                </form>
                                            </td>";
                                        echo "</tr>";
                                    }

                                    $isThereMember = true;
                                } else {
                                    echo "<tr><td class='no-result-label text-center' colspan='7'>No members found</td></tr>";
                                    $isThereMember = false;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
    
            </div>

            <div class="right right-section member-information-section <?php echo ((!isset($memInfo)) ? 'hidden' : '')?>">
                <div class="member-header">
                    <div class="title">Member Information</div>
                    <div class="content">
                        <?php
                            if (empty($memInfo["profile"])) {
                                $imgSrc = './img/default-profile.png';
                            } else {
                                $imgSrc = getImageSrc($memInfo["profile"]);
                            }
                            echo "<img class='profile' src='$imgSrc'>";
                        ?>
                        <div class="right">
                            <p class="name data"><?php echo $memInfo['name']?></p>
                            <div class="other-info">
                                <p class="data">ID: <span class="value"><?php echo $memInfo['mem_id']?></p>
                                <p>|</p>
                                <p class="data">Age: <span class="value"><?php echo $memInfo['age']?></p>
                                <p>|</p>
                                <p class="data">Sex: <span class="value"><?php echo $memInfo['sex']?></p>
                            </div>
                            <div class="other-info">
                                <p class="data"> <?php if ($memInfo['contact']!=""){echo $memInfo['contact'];}else{echo 'null';} ?></p>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="member-body">
                    <hr>
                    <div class="info">
                        <p class="label">Total Savings: </p>
                        <p class="data">
                            <span class="detail">₱<?php echo ((isset($memInfo['mem_id'])) ? getTotalDeposits($conn, $memInfo['mem_id']) : 0 ); ?></span>
                        </p>
                    </div>
                    <div class="info">
                        <p class="label">Loan Balance: </p>
                        <p class="data">
                            <span class="detail">₱<?php echo ((isset($memInfo['mem_id'])) ? getTotalLoanBalance($conn, $memInfo['mem_id']) . " <span class='c-gray small-text'>(+ ₱" . getTotalInterests($conn, $memInfo['mem_id']) . ' Interests)</span>' : 0);?></span>
                        </p>
                    </div>
                    <div class="info">
                        <p class="label">Interest Share: </p>
                        <p class="data">
                            <span class="detail">₱<?php echo number_format(getMemberInterestsShare($conn), 2); ?></span>
                        </p>
                    </div>
                </div>
                <div class="member-footer">
                    <div class="title">Other Info</div>
                    <hr>
                    <div class="content">
                        <p class="info"><span class="label">Username: </span><span class="value"><?php echo $memInfo['username']; ?></span></p>
                        <p class="info"><span class="label">Address: </span><span class="value"><?php if ($memInfo['address']!=""){echo $memInfo['address'];}else{echo 'null';} ?></span></p>
                        <p class="info"><span class="label">Added On: </span><span class="value"><?php echo $memInfo['date_added']; ?></span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="section second">
        <h3 class="title">2. Enter Amount</h3>
        <div class="content">
            <form action="./database/deposit-savings.php" method="POST">
                <div class="info">
                    <label for="savings-amount">Amount: (₱)</label>
                    <input type="number" id="savings-amount" class="no-spinner" placeholder="<?php if(!$isThereMember || empty($memInfo['mem_id'])) {echo "Disabled";} else {echo "Enter here";}?>" name="deposits-amount" <?php if(!$isThereMember || empty($memInfo['mem_id'])) echo "disabled"?>>
                    <input type="hidden" name="mem_id" value="<?php if (isset($memInfo['mem_id'])) {echo $memInfo['mem_id']; }?>">
                </div>
                <button class="submit" type="submit" name="submit" value="submit" <?php if(!$isThereMember || empty($memInfo['mem_id'])) echo "disabled"?>>Save</button>
            </form>
        </div>
    </div>
</div>
