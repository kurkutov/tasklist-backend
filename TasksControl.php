<?php

	class TasksControl {

		public $pdo;

		function __construct($host, $dbname, $dbuser, $pass) {
			try {
				$this->pdo = new PDO(
					"mysql:
					host={$host};
					dbname={$dbname};
					charset=utf8;",
					$dbuser,
					$pass);
			} catch (Exception $e) {
				die('Невозможно установить соединение с базой данных');
			}

		}

		public function add($task, $state_id = 0, $search_string = '') {
			$stmt = $this->pdo->prepare("INSERT INTO `tasks`(
					`description`, 
					`state_id`, 
					`priority_id`, 
					`planned_end_date`, 
					`actual_end_date`) 
				VALUES (
					:description,
					:state_id,
					:priority_id,
					:planned_end_date,
					:actual_end_date)");
			$stmt->execute([
				':description' => $task['description'],
				':state_id' => $task['state_id'],
				':priority_id' => $task['priority_id'],
				':planned_end_date' => $task['planned_end_date'],
				':actual_end_date' => $task['actual_end_date']
			]);
			if ($count = $stmt->rowCount() > 0) {
				return $this->init($state_id, $search_string);
			} else {
				return false;
			}
		}

		public function update($task, $state_id = 0, $search_string = '') {
			$stmt = $this->pdo->prepare("UPDATE `tasks` SET 
				`description`=:description,
				`state_id`=:state_id,
				`priority_id`=:priority_id,
				`planned_end_date`=:planned_end_date,
				`actual_end_date`=:actual_end_date
				WHERE `id` = :id");
			$stmt->execute([
				':id' => $task['id'],
				':description' => $task['description'],
				':state_id' => $task['state_id'],
				':priority_id' => $task['priority_id'],
				':planned_end_date' => $task['planned_end_date'],
				':actual_end_date' => $task['actual_end_date']
			]);
			if ($count = $stmt->rowCount() > 0) {
				return $this->init($state_id, $search_string);
			} else {
				return false;
			}
		}

		private function get_data($query, $fetch = 'all', $fetch_style = PDO::FETCH_ASSOC) {

			$stmt = $this->pdo->prepare($query);
			$stmt->execute();
			switch ($fetch) {
				case 'all':
					return $stmt->fetchAll($fetch_style);
					break;
				case 'column':
					return $stmt->fetchColumn();
					break;
				default:
					return $stmt->fetch($fetch_style);
					break;
			}
		}

		public function init($state_id = 0, $search_string = '') {



			$states = $this->get_task_options("states");
			$priorities = $this->get_task_options("priorities");

			if ($search_string !== '') {
				$tasks =  $this->search($search_string);
			} else {
				$tasks =  $this->get_tasks($state_id);	
			}
			
			return [
				"states" => $states,
				"priorities" => $priorities,
				"tasks" => $tasks
			];
		}

		public function get_statuses() {
			$result = [];
			// кол-во всех задач
			$count = $this->get_data("SELECT COUNT(*) FROM `tasks`;", 'column');

			$query = "SELECT 
				`states`.`id`,
			    `states`.`state`,
			    COUNT(`tasks`.`description`) as quantity
				FROM `states`
				JOIN `tasks` ON `tasks`.`state_id` = `states`.`id`
				GROUP BY `states`.`id`";

			$result = $this->get_data($query);

			// добавляем элемент, "Всего" в начало массива
			array_unshift($result, [
				"id" => "0",
				"state" => "Всего",
				"quantity" => $count
			]);

			return $result;
			
		}

		public function get_task_options($option) {

			$query = "SELECT 
				`{$option}`.`id`,
			    `{$option}`.`name`,
			    COUNT(`tasks`.`id`) as quantity
				FROM `{$option}`
				LEFT OUTER JOIN `tasks` ON `tasks`.`state_id` = `{$option}`.`id`
				GROUP BY `{$option}`.`id`";

			return $this->get_data($query);
		}
		
		public function get_tasks($state_id) {

			$query = "SELECT `tasks`.`id`, `description`, `state_id`, `priority_id`, `planned_end_date`, `actual_end_date` FROM `tasks`";

			if ($state_id > 0) {
				$query .= " JOIN `states` ON `tasks`.`state_id` = `states`.`id` WHERE `states`.`id` = '{$state_id}'";
			}

			return $this->get_data($query);
		}

		public function search($search_string) {
			$query = "SELECT * 
				FROM `tasks` 
				WHERE `tasks`.`description` LIKE '%{$search_string}%'";

			return $this->get_data($query);
		}

		public function delete($task_id, $state_id = 0, $search_string = '') {

			$stmt = $this->pdo->prepare("DELETE FROM `tasks` WHERE `id` = :id");
			$stmt->execute([':id' => $task_id]);

			if ($stmt->rowCount() > 0) {
				return $this->init($state_id, $search_string);
			} else {
				return false;
			}


		}


	}