<?php
require('../common/variables.php');
function genSN($conn)
{
    do {
        $sn = chr(rand(65, 90));
        $sn .= substr(time(), 1, 9);

        $stmt = oci_parse($conn, "SELECT SN FROM SN_DB WHERE SN=:sn");
        oci_bind_by_name($stmt, ':sn', $sn);
        oci_execute($stmt, OCI_DEFAULT);
        $data = oci_fetch($stmt);
        oci_free_statement($stmt);
    } while ($data); //若已存在相同序號,則重新產生

    return $sn;
}
function genPassword()
{
    $digit = 0;
    $pwd = "";
    while ($digit < 10) {
        $type_c = rand(1, 2);
        switch ($type_c) {
            case 1:
                $pwd .= chr(random_int(48, 57));
                break;
            case 2:
                $pwd .= chr(random_int(65, 90));
                break;
        }
        $digit++;
    }

    return $pwd;
}
function genOrder($graduated)
{
    $ORG_NO = "99216"; // what is this?

    if ($graduated === 2) //曾報考當年度碩推者
    {
        $SIGNUP_FEE = "0"; //免繳報名費
        $acc_file = "./acc/" .  "signup_acc_9.txt"; //帳號為99216-39xxxxxx-x
    } else if ($_POST['dept_id'] === "2") //1300
    {
        if ($_POST['identity'] === "1") //一般考生
        {
            if ($graduated === 1) //校友
            {
                $SIGNUP_FEE = "1040"; //一般考生(校友)1040
                $acc_file = "./acc/" .  "signup_acc_2.txt"; //帳號為99216-32xxxxxx-x

            } else {
                $SIGNUP_FEE = "1300"; //一般考生1300
                $acc_file = "./acc/" .  "signup_acc_1.txt"; //帳號為99216-31xxxxxx-x

            }
        } else if ($_POST['identity'] === "2") {
            $SIGNUP_FEE = "0"; //中低收入戶0
            $acc_file = "./acc/" .  "signup_acc_3.txt"; //帳號為99216-33xxxxxx-x
        } else if ($_POST['identity'] === "3") {
            $SIGNUP_FEE = "0"; //低收入戶0
            $acc_file = "./acc/" .  "signup_acc_4.txt"; //帳號為99216-34xxxxxx-x
        }
    } else if ($_POST['dept_id'] === "1") //1800
    {
        if ($_POST['identity'] === "1") //一般考生
        {
            if ($graduated === 1) //校友
            {
                $SIGNUP_FEE = "1440"; //一般考生(校友)1440
                $acc_file = "./acc/" .  "signup_acc_6.txt"; //帳號為99216-36xxxxxx-x
            } else {
                $SIGNUP_FEE = "1800"; //一般考生1800
                $acc_file = "./acc/" .  "signup_acc_5.txt"; //帳號為99216-35xxxxxx-x

            }
        } else if ($_POST['identity'] === "2") {
            $SIGNUP_FEE = "0"; //中低收入戶0
            $acc_file = "./acc/" .  "signup_acc_7.txt"; //帳號為99216-37xxxxxx-x
        } else if ($_POST['identity'] === "3") {
            $SIGNUP_FEE = "0"; //低收入戶0
            $acc_file = "./acc/" .  "signup_acc_8.txt"; //帳號為99216-38xxxxxx-x
        }
    } else {
        $dept_id = $_POST['dept_id'];
        mail('bob@cc.ncue.edu.tw', 'dept_id(碩士班)', $dept_id);
        throw new Exception("報考系所資料錯誤，請重新填寫！", 400);
    }

    $fp = fopen($acc_file, "r");
    $p_acc = trim(fgets($fp, 2048)) + 1;
    fclose($fp);

    $acc = $ORG_NO . $p_acc;

    //檢查碼運算
    $checksum_1 = 0;
    $checksum_2 = 0;
    $times = 1;
    $len_1 = strlen($acc) - 1;
    $len_2 = strlen($SIGNUP_FEE) - 1;
    while ($len_1 >= 0) {
        $bit_1 = substr($acc, $len_1, 1);
        $checksum_1 += ($bit_1 * $times);
        if ($len_2 >= 0) {
            $bit_2 = substr($SIGNUP_FEE, $len_2, 1);
            $checksum_2 += ($bit_2 * $times);
            $len_2--;
        }
        $times++;
        if ($times == 10) {
            $times = 1;
        }
        $len_1--;
    }
    $checksum = $checksum_1 + $checksum_2;
    $checksum = substr($checksum, (strlen($checksum) - 1), 1);
    $account_f = $ORG_NO . "-" . $p_acc . "-" . $checksum;


    return array('account_no' => $account_f, 'pay_money' => $SIGNUP_FEE, 'acc_file' => $acc_file, 'p_acc' => $p_acc);
}

function checkGraduated($conn, $id, $ACT_YEAR_NO)
{
    $graduated = 0;
    if (strlen($id) === 10) {
        //曾報考當年度碩推者(29x為博推),免繳報名費
        $sql = "SELECT count(*) from signupdata WHERE  id=:id and SCHOOL_ID='2' and year=:ACT_YEAR_NO and substr(dept_id,1,2)<>'29'";
        $stmt = oci_parse($conn, $sql);
        $params = array(':id' => $id, ':ACT_YEAR_NO' => $ACT_YEAR_NO);
        foreach ($params as $key => $val)
            oci_bind_by_name($stmt, $key, $params[$key]);
        oci_execute($stmt, OCI_DEFAULT);
        oci_fetch($stmt);
        $nrows = oci_result($stmt, 1); //$nrows -->總筆數
        if ($nrows > 0) {
            $graduated = 2;
        } /*else {
                //96年以前在職專班畢業生(尚未電腦化)
                //需再修改account_c.php
                if ($id === "L120210922" || $id === "Q220098486" || $id === "N121851563" || $id === "N220261405" || $id === "N122009956") {
                    $graduated = 1;
                }
                //畢業生報名費打八折(106/12/6淑琬確認--限在學,畢業生,延長修業 , 108/09/19 淑琬要求排除學分班(94))
                $sql = "SELECT count(*) from dean.s30_student WHERE  stu_idno=:id and stu_status in ('1','8','29')  and substr(stu_id,4,2)<>'94' ";
                $stmt = oci_parse($conn, $sql);
                $params = array(':id' => $id);
                foreach ($params as $key => $val)
                    oci_bind_by_name($stmt, $key, $params[$key]);
                oci_execute($stmt, OCI_DEFAULT);
                oci_fetch($stmt);
                $nrows = oci_result($stmt, 1); //$nrows -->總筆數
                if ($nrows > 0) {
                    $graduated = 1;
                } else {
                    $graduated = 0;
                }
            }*/
    }

    return $graduated;
}
