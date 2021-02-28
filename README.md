# 國立彰化師範大學網路報名系統
API Documentation：https://wei032499.github.io/exampg/

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/66c5dba4d9a9998ff21d)

## 概述(Overview)
1. 以存放在cookie的JSON Web Token (JWT)作權限驗證。
1. 利用browser的sessionStorage做資料的暫存(表單確認頁)。

## 檔案名稱
「系統公告」：`index.php`。

「流程說明」：`intro_registration.php`、`intro_payment.php`。

「網路報名」：`order.php`、`signup.php`、`alter.php`、`confirm.php`、`letter.php`。(相關內頁於`./signup/`)

「資料查詢」：`query_acc.php`、`query_score.php`、`query_pwd.php`、`query_signup.php`、`letter.php`。(相關內頁於`./query/`)

「報到相關」：`enroll_list.php`、`enroll_queue.php`。(相關內頁於`./enroll/`)

「罕用字說明」：`intro_sw.php`。

## 前端(Front-end)
#### common.js (`./js/common.js`)：(定義共用的函數)
    初始化(on ready)：**自動綁定某些button的功能**
    URLSearchParams：URL參數的解析。(部分瀏覽器已有定義)
    getSessionItems：將儲存於sessionStorage的serialized form轉換為object
    getData：從指定url取得data object
    fillForm：將data object寫回form
    getCookie：取得cookie中指定key的value
    logout：登出系統
#### signup.js (`./js/signup.js`)：(「網路報名」相關的表單功能)
    全域變數：deptObj=>可選系所、isConfirmForm=>是否為確認頁(true需轉換為readonly form)

**其餘函數功能請參照程式碼內註解

## 後端(Back-end)
#### db_account.php (`./API/common/db_account.php`)：資料庫連線資訊
#### db.php (`./API/common/db.php`)：與資料庫建立連線`$conn`
#### functions.php (`./API/common/functions.php`)：定義共用的函數
#### auth資料夾 (`./API/auth/`)：帳號驗證相關
#### common資料夾 (`./API/common/`)：共用函數與檔案
#### dept資料夾 (`./API/dept/`)：可報考系所、聯招系所與考科
#### enroll資料夾 (`./API/enroll/`)：報到相關
#### news資料夾 (`./API/news/`)：系統公告相關
#### order資料夾 (`./API/order/`)：繳費帳號相關
#### signup資料夾 (`./API/signup/`)：網路報名相關

**其餘函數功能請參照程式碼內註解

## 資料欄說明(資料庫)
#### SN_DB：
    CHECKED：是否入帳 (1：已入帳)
    SIGNUP_ENABLE：是否可進行報名
    LOCK_UP：已填寫報名表即鎖定(1)

#### SIGNUPDATA：
    LOCK_UP：是否已完成報名
    1 => 已完成報名，無法再修改

#### DEPARTMENT：
    TEST_TYPE：
    0 => 無分組(科)
    1 => 分組(科)
    2 => 不分組選考
    3 => 不分組選考(3選2)

    UPLOAD_TYPE：審查資料繳交方式
    1 => 郵寄
    2 => 上傳
    3 => 郵寄+上傳

#### SUBJECT：
    ID：
    substr(ID,1,3) => DEPT_ID
    substr(ID,1,4) => ORGANIZE_ID
    substr(ID,1,5) => ORASTATUS_ID
    substr(ID,4,1) => '0'(不分組)、'9'(不分組選考)
    substr(ID,6,1) => SECTION ('0'表示口試或審查)

## 其他相關文件
請參照`./documentation/`資料夾
