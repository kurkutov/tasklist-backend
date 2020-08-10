<?php

	## Заголовки ответа
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	header('Content-Type: application/json; charset=utf-8');

	require_once "TasksControl.php";

	$tasks_control = new TasksControl('localhost', 'db_tasklist', 'admin', 'admin');

	$req = json_decode(file_get_contents('php://input'), true);

	switch ($req['action']) {
		case 'init':
			// $res = get_tasks($req['state_id']);
			$res = $tasks_control->init($req['state_id'], $req['search_string']);
			break;
		case 'tasks':
			// $res = get_tasks($req['state_id']);
			$res = $tasks_control->get_tasks($req['state_id']);
			break;
		case 'search':
			$res = $tasks_control->search($req['search_string']);
			break;
		case 'statuses':
			$res = $tasks_control->get_task_options('states');
			break;
		case 'priorities':
			$res = $tasks_control->get_task_options('priorities');
			break;
		case 'add':
			$res = $tasks_control->add($req['data'], $req['state_id'], $req['search_string']);
			break;
		case 'update':
			$res = $tasks_control->update($req['data'], $req['state_id'], $req['search_string']);
			break;			
		case 'delete':
			$res = $tasks_control->delete($req['task_id'], $req['state_id'], $req['search_string']);
			break;
	}
	echo json_encode($res);
 ?>