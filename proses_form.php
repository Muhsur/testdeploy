<?php
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nama = trim($_POST['nama'] ?? '');
	$komentar = trim($_POST['komentar'] ?? '');

	if ($nama === '' || $komentar === '') {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'message' => 'Nama dan komentar wajib diisi.'
		]);
		exit;
	}

	$stmt = mysqli_prepare($conn, 'INSERT INTO comments (nama, komentar) VALUES (?, ?)');

	if (!$stmt) {
		http_response_code(500);
		echo json_encode([
			'success' => false,
			'message' => 'Gagal menyiapkan query.'
		]);
		exit;
	}

	mysqli_stmt_bind_param($stmt, 'ss', $nama, $komentar);

	if (!mysqli_stmt_execute($stmt)) {
		http_response_code(500);
		echo json_encode([
			'success' => false,
			'message' => 'Gagal menyimpan komentar.'
		]);
		mysqli_stmt_close($stmt);
		exit;
	}

	mysqli_stmt_close($stmt);

	echo json_encode([
		'success' => true,
		'message' => 'Komentar berhasil disimpan.'
	]);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$result = mysqli_query($conn, 'SELECT nama, komentar, created_at FROM comments ORDER BY id DESC');

	if (!$result) {
		http_response_code(500);
		echo json_encode([
			'success' => false,
			'message' => 'Gagal mengambil data komentar.'
		]);
		exit;
	}

	$comments = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$comments[] = $row;
	}

	echo json_encode([
		'success' => true,
		'data' => $comments
	]);
	exit;
}

http_response_code(405);
echo json_encode([
	'success' => false,
	'message' => 'Method tidak didukung.'
]);
