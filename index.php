<?php

require 'vendor/autoload.php';

$guard = Models\sf_guard_user_group::query()
//        ->where('is_active','=','1')
//        ->has('sfguard.is_active','=','1',true)
//        ->has('sfguard.is_active','=','1',true)
        ->where('group_id', '=', '2')
;
$result = $guard->get();

$student = Models\student::query()->where('id', '=', $mat->student_id)->get();

//// Output the LDIF object as a string to a file.
////file_put_contents('/path/to/ldif.txt', $ldif->toString());
//// Output the LDIF to a string and do whatever you need with it...
//$ldifData = $ldif->toString();
//echo $ldifData;
// cn=00387046259,ou=dti,dc=uerr,dc=edu,dc=br