<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//
define("DBHost","localhost");
define("DBUser","root");

//define("DBPasswd","coolpix4900");         //For Server PCS
define("DBPasswd","");                  //For Test

define("DBName","pcs_carlendar");
//define("DBNameTraining","training_stocktake");
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");
define("SECURE", FALSE);    // For development purposes only!!!!
define("RecordsPerPage",100);
define("LocationMaxLength","4");
define("AssetPicture","isd");
define("CompanyAcronym","ระบบบันทึกข้อมูลการทำงาน");
define("CompanyName","R2");
define('LINE_API',"https://notify-api.line.me/api/notify");
//define('Token',"3BeAevZV4mQGfDcCb2znFS46MGRIn4jXKQmdR7zkNWT");          //For Real
define('Token',"3AGqySs6dalnP8WRB5gGYw6dHYYgvJFP6TEzDW4fKAT");          //For Test
//define("PathStaffPicture","file:///P:/");  
//define("PathAssetPicture","/var/www/harrowassets/assetimages/full/"); 
?>
