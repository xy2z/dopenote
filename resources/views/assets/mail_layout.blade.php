<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
	</head>
	<body style="background: #ddd;">
		<main style="font-family: Arial; background: #fff; width: 500px; max-width: 100%; padding: 40px; margin: 40px auto; border-radius: 10px; box-shadow: 2px 2px 2px #bbb; word-break: break-word;">
			@yield('content')
		</main>
	</body>
</html>