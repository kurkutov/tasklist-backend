<?php

	require_once "connect.php";

	function get_data($query, $fetch = 'all', $fetch_style = PDO::FETCH_ASSOC) {

		try {
			$stmt = $pdo->prepare($query);
			$stmt->execute();
			switch ($fetch) {
				case 'all':
					return $stmt->fetchAll($fetch_style);
					break;
				case 'column':
					return $stmt->fetchColumn($fetch_style);
					break;
				default:
					return $stmt->fetch($fetch_style);
					break;
			}
		} catch (PDOException  $e) {
			die("<strong>Ошибка!</strong> Не удалось загрузить данные");
		}	
	}

	function get_statuses() {
		$result = [];
		// кол-во всех задач
		$count = get_data("SELECT COUNT(*) as quantity FROM `tasks`", 'column');	

		$query = "SELECT 
			`states`.`id`,
		    `states`.`state`,
		    COUNT(`tasks`.`description`) as quantity
			FROM `states`
			JOIN `tasks` ON `tasks`.`state_id` = `states`.`id`
			GROUP BY `states`.`id`";

		$result = get_data($query);		

		// добавляем элемент, "Всего" в начало массива
		array_unshift($result, [
			"id" => "0",
			"state" => "Всего",
			"quantity" => $count
		]);

		return $result;
	}
	
	function get_tasks($state_id) {

		$query = "SELECT
			`tasks`.`id`,
			`tasks`.`description`,
			`states`.`state`,
			`priorities`.`priority`,
			`tasks`.`planned_end_date`,
			`tasks`.`actual_end_date`
			FROM `tasks` 
			JOIN `states` ON `tasks`.`state_id` = `states`.`id`
			JOIN `priorities`ON `tasks`.`priority_id` = `priorities`.`id`";

			if ($state_id > 0) {
				$query .= " WHERE `states`.`id` = '{$state_id}'";
			}

			return get_data($query);
	}

	function search($search_string) {
		$query = "SELECT
			`tasks`.`id`,
			`tasks`.`description`,
			`states`.`state`,
			`priorities`.`priority`,
			`tasks`.`planned_end_date`,
			`tasks`.`actual_end_date`
			FROM `tasks` 
			JOIN `states` ON `tasks`.`state_id` = `states`.`id`
			JOIN `priorities`ON `tasks`.`priority_id` = `priorities`.`id`
			WHERE `tasks`.`description` LIKE '%{$search_string}%'";

		return get_data($query);
	}