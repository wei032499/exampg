# 國立彰化師範大學網路報名系統
API Documentation：https://wei032499.github.io/exampg/

## 資料欄說明
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