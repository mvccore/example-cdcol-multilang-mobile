<?php

namespace App\Models;

class Album extends \MvcCore\Model
{
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
			FROM
				cds AS c
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
			FROM
				cds as c
			WHERE
				c.id = :id
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
		$this->Init();
		$update = $this->db->prepare("
			DELETE FROM
				cds
			WHERE
				id = :id
		");
		return $update->execute([
			":id"	=> $this->Id,
		]);
	}
	/**
	 * Update album with completed Id or insert new one if no Id defined.
	 * Return Id as result.
	 * @return int
	 */
	public function Save () {
		$this->Init();
		if (isset($this->Id)) {
			$this->update();
		} else {
			$this->Id = $this->insert();
		}
		return $this->Id;
	}

	/**
	 * Update all public defined properties.
	 * @return bool
	 */
	protected function update () {
		$update = $this->db->prepare("
			UPDATE
				cds
			SET
				interpret = :interpret,
				year = :year,
				title = :title
			WHERE
				id = :id
		");
		return $update->execute([
			":interpret"	=> $this->Interpret,
			":year"			=> $this->Year,
			":title"		=> $this->Title,
			":id"			=> $this->Id,
		]);
	}

	/**
	 * Insert only filled values, return new album id.
	 * @return int
	 */
	protected function insert() {
		$columnsSql = [];
		$params = [];
		foreach ($this->GetValues() as $key => $value) {
			$keyUnderscored = \MvcCore\Tool::GetUnderscoredFromPascalCase($key);
			$columnsSql[] = $keyUnderscored;
			$params[':' . $keyUnderscored] = $value;
		}
		$sql = 'INSERT INTO cds (' . implode(',', $columnsSql) . ')
			 VALUES (:' . implode(', :', $columnsSql) . ')';
		$insertCommand = $this->db->prepare($sql);
		$insertCommand->execute($params);
		return (int) $this->db->lastInsertId();
	}
}
