function formhash(form, password) {

    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "password";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    
    // Finally submit the form. 
    form.submit();
}


function regformhash(form, staffid, passwordOld, passwordNew, conf) {
    
    // Check each field has a value
    if (staffid.value == '' || passwordOld.value == '' || passwordNew.value == '' || conf.value == '') {
        alert('คุณระบุข้อมูลไม่ครบ ลองใหม่อีกครั้ง');
        return false;
    }
    
    // Check the username
    re = /^\w+$/; 
//    if(!re.test(form.username.value)) { 
//        alert("Username must contain only letters, numbers and underscores. Please try again"); 
//        form.username.focus();
//        return false; 
//    }
    
    // Check that the password is sufficiently long (min 6 chars)
    // The check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (passwordNew.value.length < 5) {
        alert('รหัสจะต้อง 5 หลักขึ้นไป');
        form.password.focus();
        return false;
    }
    
    // At least one number, one lowercase and one uppercase letter 
    // At least six characters 
    // var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    // if (!re.test(passwordNew.value)) {
    //     alert('รหัสผ่านจะต้องมีตัวเลข อย่างน้อย 1 หลักขึ้นไป และมีตัวอักษรภาษาอังกฤษพิมพ์ใหญ่ อย่างน้อย 1 ตัว และตัวพิมพ์เล็ก อย่างน้อย 1 ตัว  ลองใหม่อีกครั้ง');
    //     return false;
    // }
    
    // Check password and confirmation are the same
    if (passwordNew.value != conf.value) {
        alert('รหัสที่ระบุไม่ตรงกัน ลองใหม่อีกครั้ง');
        form.password.focus();
        return false;
    }

    // Create a new element input, this will be our hashed password field. 
    var pNew = document.createElement("input");
    var pOld = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(pNew);
    pNew.name = "passwordNew";
    pNew.type = "hidden";
    pNew.value = hex_sha512(passwordNew.value);
    
    form.appendChild(pOld);
    pOld.name = "passwordOld";
    pOld.type = "hidden";
    pOld.value = hex_sha512(passwordOld.value);

    // Make sure the plaintext password doesn't get sent. 
    passwordNew.value = "";
    passwordOld.value = "";
    conf.value = "";
 
    // Finally submit the form. 
    form.submit();
    return true;
}

function regformhash_ForAdmin(form, staffid, password, conf) {
    
    // Check each field has a value
    if (staffid.value == '' ||  password.value == '' || conf.value == '') {
        alert('คุณระบุข้อมูลไม่ครบ ลองใหม่อีกครั้ง');
        return false;
    }
    
    // Check the username
    re = /^\w+$/; 
//    if(!re.test(form.username.value)) { 
//        alert("Username must contain only letters, numbers and underscores. Please try again"); 
//        form.username.focus();
//        return false; 
//    }
    
    // Check that the password is sufficiently long (min 6 chars)
    // The check is duplicated below, but this is included to give more
    // specific guidance to the user
    if (password.value.length < 6) {
        alert('รหัสจะต้อง 6 หลักขึ้นไป');
        form.password.focus();
        return false;
    }
    
    // At least one number, one lowercase and one uppercase letter 
    // At least six characters 
    var re = /(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}/; 
    if (!re.test(password.value)) {
        alert('รหัสผ่านจะต้องมีตัวเลข อย่างน้อย 1 หลักขึ้นไป และมีตัวอักษรภาษาอังกฤษพิมพ์ใหญ่ อย่างน้อย 1 ตัว และตัวพิมพ์เล็ก อย่างน้อย 1 ตัว  ลองใหม่อีกครั้ง');
        return false;
    }
    
    // Check password and confirmation are the same
    if (password.value != conf.value) {
        alert('รหัสที่ระบุไม่ตรงกัน ลองใหม่อีกครั้ง');
        form.password.focus();
        return false;
    }

    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");

    // Add the new element to our form. 
    form.appendChild(p);
    p.name = "password";
    p.type = "hidden";
    p.value = hex_sha512(password.value);

    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
    conf.value = "";
 
    // Finally submit the form. 
    form.submit();
    return true;
}
