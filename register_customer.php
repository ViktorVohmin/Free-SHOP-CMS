<?php

// Чтение настроек подключения из config.ini
$config = parse_ini_file('config.ini');

// Подключение к базе данных PostgreSQL с использованием PDO
try {
    $pdo = new PDO("pgsql:host=" . $config['host'] . ";dbname=" . $config['dbname'], $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to the database: " . $e->getMessage());
}

// Проверяем, что форма была отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы регистрации
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $delivery_address = $_POST["delivery_address"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];
    $password = $_POST["password"];

    // Хешируем пароль для безопасного хранения
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Вставляем данные в таблицу customers
    $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, delivery_address, email, phone_number, password_hash, user_type_id) 
                          VALUES (:first_name, :last_name, :delivery_address, :email, :phone_number, :password, 3)");
    
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':delivery_address', $delivery_address);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':password', $hashed_password);
    
	try {
		$stmt->execute();
		header("Location: index.php?registration=success"); // Перенаправляем на index.php с параметром success
		exit; // Убедитесь, что после перенаправления больше ничего не выполняется
	} catch (PDOException $e) {
		header("Location: index.php?error=" . urlencode($e->getMessage())); // Перенаправляем на index.php с параметром error содержащим текст ошибки
		exit;
	}
}
?>
