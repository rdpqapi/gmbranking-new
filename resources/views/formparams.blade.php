<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="{{url('cseranking')}}" method="get" >
        <input type="text" name="keyword" placeholder="Keyword" required>
        <input type="text" name="city" placeholder="City">
        <input type="text" name="country" placeholder="Country">
        <input type="text" name="burl" placeholder="URL (example.com)" required>
	<select name="txtlanguage">
	@foreach($datas as $data)
            <option value="{{$data->lang_code}}" {{($data->lang_code=='lang_en')?'selected':''}}>{{$data->lang_name}}</option>

@endforeach
        </select>

        <p>
    <input type="submit" value="Continue">
</p>
    </form>
</body>
</html>