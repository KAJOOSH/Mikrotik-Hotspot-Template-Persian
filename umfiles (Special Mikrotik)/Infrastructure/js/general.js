function checkEmail(objid) {
    var obj = document.getElementById(objid);
    if (!obj) return false;
    var eregex  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;    
    if (!eregex.test(obj.value)) {
        alert("Invalid email address");
        obj.focus();
        return false;
    }
    return true;
}

function checkMinLen(objid, objname, minlen) {
    var obj = document.getElementById(objid);
    if (!obj) return false;
    var v = "" + obj.value;
    if (v.length < minlen) {
        alert(objname + " must be at least " + minlen + " characters long");
        obj.focus();
        return false;
    }
    return true;
}

function checkAreEqual(obj1id, obj2id, errmsg) {
    var obj1 = document.getElementById(obj1id);
    if (!obj1) return false;
    var obj2 = document.getElementById(obj2id);
    if (!obj2) return false;
    if (obj1.value != obj2.value) {
	alert(errmsg);
	obj2.focus();
	return false;
    }
    return true;
}

