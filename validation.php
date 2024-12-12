<?PHP

function check_number($number)
{
 if(!is_numeric($number))
    {
   header("location:javascript://history.go(-1)");
   exit(); 
}
else {
	return $number;
}
}

function check_string_dashes($text)
{
	if(!(preg_match('/^[a-zA-Z\'., -|&|\(\)\/]+$/', $text)))
	{
		header("location:javascript://history.go(-1)");
   exit();
	}
	else{
		return $text;
	}
}

function check_string_numbers($text)
{
	if(!(preg_match('/^[a-zA-Z0-9 _.-<>|&|]+$/', $text)))
	{
		header("location:javascript://history.go(-1)");
   exit();
	}
	else{
		return $text;
	}
}

function check_date($date)
{
	if(!(preg_match('/^[0-9-\/]+$/', $date)))
	{
		header("location:javascript://history.go(-1)");
                exit();
	}
	else{            
		return $date;
	}
}

function alphanumeric_underscore($var)
{
	if(!(preg_match('/^[a-zA-Z0-9_ ]+$/', $text)))
	{
		header("location:javascript://history.go(-1)");
   exit();
	}
	else{
		return $var;
	}
}

?>
