<?php

namespace App\Models;

class Album extends \MvcCore\Model {

	/** @var int */
	public $Id;
	/** @var string */
	public $Title;
	/** @var string */
	public $Interpret;
	/** @var int */
	public $Year;

	/**
	 * Get all albums in database as array, keyed by $album->Id.
	 * @return \MvcCore\Model[]
	 */
	public static function GetAll () {
		$select = self::GetConnection()->prepare("
			SELECT
				c.id AS Id,
				c.title AS Title,
				c.interpret AS Interpret,
				c.year AS Year
			FROM cds AS c
		");
		$select->execute();
		$albums = [];
		while ($album = $select->fetchObject('\App\Models\Album'))
			$albums[] = $album;
		return $albums;
	}

	/**
	 * Get single album instance by given id or null if no record by id in database.
	 * @param int $id
	 * @return \MvcCore\Model|null
	 */
	public static function GetById ($id) {
		$select = self::GetConnection()->prepare("
			SELECT
				c.id AS Id,
				c.title AS Title,
				c.interpret AS Interpret,
				c.year AS Year
			FROM cds as c
			WHERE c.id = :id
		");
		$select->execute([
			":id" => $id,
		]);
		return $select->fetchObject('\App\Models\Album');
	}

	/**
	 * Delete database row by album Id. Return affected rows count.
	 * @return bool
	 */
	public function Delete () {
		return self::GetConnection()
			->prepare("DELETE FROM cds WHERE id = :id;")
			->execute([":id" => $this->Id]);
	}

	/**
	 * Update album with completed Id or insert new one if no Id defined.
	 * Return Id as result.
	 * @return bool
	 */
	public function Save () {
		if (isset($this->Id)) {
			return $this->update();
		} else {
			return $this->insert();
		}
	}

	/**
	 * Update all public defined properties.
	 * @return bool
	 */
	protected function update () {
		$data = $this->GetTouched(
			\MvcCore\IModel::PROPS_PUBLIC |
			\MvcCore\IModel::PROPS_CONVERT_PASCALCASE_TO_UNDERSCORES
		);
		$colsSqlItems = [];
		$params = [];
		foreach ($data as $columnName => $value) {
			if ($columnName === 'id') continue;
			$colsSqlItems[] = "{$columnName} = :{$columnName}";
			$params[":{$columnName}"] = self::convertToScalar($columnName, $value);
		}
		$params[':id'] = $this->Id;
		$colsSql = implode(", ", $colsSqlItems);
		return self::GetConnection()
			->prepare("UPDATE cds SET {$colsSql} WHERE id = :id;")
			->execute($params);
	}

	/**
	 * Insert only filled values and complete new album id.
	 * @return bool
	 */
	protected function insert () {
		$columnsSql = [];
		$params = [];
		$data = $this->GetValues(
			\MvcCore\IModel::PROPS_PUBLIC |
			\MvcCore\IModel::PROPS_CONVERT_PASCALCASE_TO_UNDERSCORES
		);
		foreach ($data as $columnName => $value) {
			$columnsSql[] = $columnName;
			$params[":{$columnName}"] = self::convertToScalar($columnName, $value);
		}
		$sql = 'INSERT INTO cds (' . implode(',', $columnsSql) . ')
			 VALUES (:' . implode(', :', $columnsSql) . ')';
		$db = self::GetConnection();
		try {
			$db->beginTransaction();
			$db
				->prepare($sql)
				->execute($params);
			$newId = $db->lastInsertId();
			$db->commit();
			$this->Id = $newId;
		} catch (\Throwable $e) {
			$db->rollBack();
			throw $e;
		}
		return TRUE;
	}
}
