<?php
//Authenticate if a user is logged in or not
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
    
    $member_type = $_GET['member_type'];

    if ($searchType == 'name') {
        //searchtype is name
        $sql = "SELECT *, members.mem_id, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.profile 
                FROM members
                LEFT JOIN accounts ON members.mem_id = accounts.mem_id 
                WHERE CONCAT(members.fname, ' ', members.lname) 
                LIKE '%$searchValue%' 
                AND ('$member_type' = 'all' OR members.is_temp_mem = '$member_type')";

    } else {
        //searchtype is mem_id
        $sql = "SELECT *, members.mem_id, CONCAT(members.fname, ' ', members.lname) AS name, members.sex, TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) AS age, accounts.profile 
                FROM members
                LEFT JOIN accounts ON members.mem_id = accounts.mem_id 
                WHERE members.$searchType LIKE '%$searchValue%' 
                AND ('$member_type' = 'all' OR members.is_temp_mem = '$member_type')";
    }
    $_SESSION['section'] = './administrator/member-information.php';
    $_SESSION['activeNavId'] = 'm-information';
    $_SESSION['sql_command'] = $sql;
    $_SESSION['selectedSearchType'] = $searchType;
    $_SESSION['searchValue'] = $searchValue;
    $_SESSION['member_type'] = $member_type;

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
    $member_type = $_SESSION['member_type'];
    $_SESSION['sql_command'] = null;
    $_SESSION['selectedSearchType'] = null;
    $_SESSION['searchValue'] = null;
    $_SESSION['member_type'] = null;
}
if (isset($_SESSION['mem_info'])) {
    $memInfo = $_SESSION['mem_info'];
    $_SESSION['mem_info'] = null;
}

//use to identify later if there is atleast a member fetch from database 
$isThereMember = false;
?>

<h1>Member Information</h1>
<hr>
<div class="member-information">
    <div class="left-section section">
        <form action="./administrator/member-information.php" method="GET">
            <div class="search-section">
                    <input class="search-input" type="text" class="search-input" placeholder="Search here" name="search-value" value="<?php if (isset($searchValue)) { echo $searchValue;}?>">
                    <select class="options select-input" name="search-type">
                        <option value="mem_id" class="option" <?php if (isset($searchType) && $searchType == 'mem_id') echo "selected"?>>ID</option>
                        <option value="name" class="option" <?php if (!isset($searchType) || $searchType == 'name') echo "selected"?>>Name</option>
                    </select> 
                    <select id='members-status-select' class="options select-input" name="member_type">
                        <option value="all" class="option" <?php if (isset($member_type) && $member_type == 'all' ) echo "selected"?>>All</option>
                        <option value="0" class="option" <?php if (!isset($member_type) || $member_type == '0') echo "selected"?>>Member</option>
                        <option value="1" class="option" <?php if (isset($member_type) && $member_type == '1') echo "selected"?>>Temporary</option>
                    </select> 
                    <button type="submit" class="" id='search-btn' name="search" value="search"><img src='./img/search.png'></button>
                </div>
        </form>
        <?php $members = $conn->query($sql); ?>
        <h4 class='mb-3'>Total: <span class="value"><?php echo $members->num_rows?></span></h4>
        <div class="result" id="member-information-table">

            <table class="result-table">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Member Type</th>
                        <th>Sex</th>
                        <th>Age</th> 
                        <th>Select</th> 
                    </tr>
                </thead>
                <tbody>
                <?php
                    
                
                    if ($members->num_rows > 0) {
                        while ($member = $members->fetch_assoc()) {
                            $memId = $member['mem_id'];
                            
                            $isSelected = false;

                            if (isset($memInfo['mem_id'])) {
                                if ($memId == $memInfo['mem_id']) {
                                    $isSelected = true;
                                }
                            }

                            echo "<tr>";
                            if (empty($member["profile"])) {
                                echo "<td class='profile-img'><img src='./img/default-profile.png' alt='img'></td>";
                            } else {
                                $imgSrc = getImageSrc($member['profile']);
                                echo "<td class='profile-img'><img src='$imgSrc' alt='img'></td>";
                            }

                            if ($member['is_temp_mem'] == 0) {
                                $member_type = 'Member';
                            } else {
                                $member_type = 'Temporary';
                            }

                            echo "<td>$memId</td>";
                            echo "<td>" . $member['name'] . "</td>";
                            echo "<td>" . $member_type . "</td>";
                            echo "<td>" . $member["sex"] . "</td>";
                            echo "<td>" . $member['age']. "</td>";
                            echo "<td>
                                    <form action='database/fetch_member_info.php' method='POST'>
                                        <input type='hidden' name='mem_id' value='$memId'>
                                        <input type='hidden' name='page' value='./administrator/member-information.php'>
                                        <input type='hidden' name='activeNavId' value='m-information'>
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
    
    <?php
        $memberInfoClass = "hidden";
        if (isset($memInfo)) {
            $memberInfoClass = "";
        }
    ?>
    <div class="right-section section member-information-section <?php echo $memberInfoClass?>">
        <div class="member-header">
            <div class="title">Member Information</div>
            <div class="content">
                <div class="left">
                    <?php
                        if (empty($memInfo["profile"])) {
                            $imgSrc = './img/default-profile.png';
                        } else {
                            $imgSrc = getImageSrc($memInfo["profile"]);
                        }
                        echo "<img class='profile' src='$imgSrc'>";
                    ?>
                </div>

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
                    <span class="detail">₱<?php echo getTotalDeposits($conn, $memInfo['mem_id']); ?></span>
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
                    <span class="detail">₱<?php echo number_format(getMemberInterestsShare($conn), 2);; ?></span>
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


