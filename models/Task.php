<?php
/**
 * Modèle représentant une Tache dans le système .
 * @author ARSENE TEKEU
 * @version 1.0
 * @date    25 nov 2025
 * chaque project contient:
 * - un identifiant unique,
 * - un titre,
 * - une description,
 * - une date de création,
 * - un identifiant d'utilisateur,
 * @property int $id
 * @property string $titre
 * @property string $description
 * @property DateTime $created_at
 * @property DateTime $update_at
 * @property DateTime $user_id
 * 
 */
require_once __DIR__ . '/../config/database.php';

class Task
{
    /**
     * les attributs de la class taches
     */
    private $id;
    private $titre;
    private $description;
    private $created_at;
    private $update_at;
    private $user_id;

    public function getId()
    {
        return $this->id;
    }
    public function getTitre()
    {
        return $this->titre;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdateAt()
    {
        return $this->update_at;
    }
    public function getIdUsers()
    {
        return $this->user_id;
    }
    //les setters
    /**
     * les setters
     * @param mixed $update_at
     * @return void
     */

    public function setUpdateAt($update_at)
    {
        $this->update_at = $update_at;
    }
    public function setTitre($titre)
    {
        $this->titre = $titre;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

    // Autres méthodes utiles

    /**
     * methode de creation d'une tache
     * @param $titre
     * @param $description
     * @param $user_id

     */

    public function createTask($titre, $description, $user_id)
    {
        try {
            $conn = connectDB();
            $this->validateTaskData($titre, $description, $user_id);

            $stmt = $conn->prepare("INSERT INTO tasks (titre, description,user_id,created_at,update_at) Values (:titre,:description,:user_id,NOW(),NOW())");

            // Nettoyage
            $params = [
                ':titre' => trim(strip_tags($titre)),
                ':description' => trim(strip_tags($description)),
                ':user_id' => (int) trim(strip_tags($user_id))
            ];

            $stmt->execute($params);
            $this->id = $conn->lastInsertId();

            return true;

        } catch (PDOException $e) {
            throw new Exception("task creation failed: " . $e->getMessage());
        }
    }

    /**
     * methode de modification d'une tache
     * @param $titre
     * @param $description
     * @param $user_id

     */
    public function updateTask(int $id, $titre, $description, $user_id): bool
    {
        try {
            $this->validateTaskData($titre, $description, $user_id);

            $query = "UPDATE tasks SET 
                        titre = :titre, 
                        description = :description, 
                        user_id = :user_id 
                      WHERE id = :id";

            $params = [
                ':titre' => trim(strip_tags($titre)),
                ':description' => trim(strip_tags($description)),
                ':user_id' => trim(strip_tags($user_id)),
                ':id' => $id
            ];

            $conn = connectDB();
            $stmt = $conn->prepare($query);
            return $stmt->execute($params);

        } catch (PDOException $e) {
            throw new Exception("Échec de la mise à jour de la tache.");
        }
    }
    /**
     * methode de suppression d'une tache
     * @param $id
     * @return true ou exception
     */

    public function deleteTask(int $id): bool
    {
        try {
            $conn = connectDB();
            $conn->beginTransaction();

            // Supprimer le tache
            $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $conn->commit();
            return true;
        } catch (PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw new Exception("Échec de la suppression du tache.");
        }
    }


    // Méthodes métier


    /**
     * recuperer tous les taches 
     * du nombre de tache
     * @param mixed $offset
     * @throws \Exception
     * @return array
     */
    public static function getAll()
    {
        $conn = connectDB();
        try {
            $query = "SELECT t.*, u.nom as createur_task,
                     FROM tasks t 
                     JOIN users u ON t.user_id = u.id";

            $params = [];

            $query .= " GROUP BY t.id, t.titre, t.descrittion, 
                       t.created_at, t.user_id, u.nom
                       ORDER BY t.created_at DESC";

            $stmt = $conn->prepare($query);

            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }

            $stmt->execute();
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($tasks === false) {
                throw new Exception("Erreurs lors de la recuperations des tasks");
            }

            return $tasks;
        } catch (PDOException $e) {
            throw new Exception("Erreurs lors de la recuperations des tasks");
        }
    }



    /**
     * recuperer une tache en fonction de son identifiant
     * @param $id
     * @return $task
     */
    public function getTasksById(int $id): ?array
    {
        $conn = connectDB();
        try {
            $stmt = $conn->prepare("SELECT * FROM tasks Where id=:id ");
            $stmt->execute([':id' => $id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($project) {
                return $project;
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("tache non existant !");
        }
    }

    private function validateTaskData($titre, $description, $user_id): void
    {
        $data = [
            'titre' => $titre,
            'description' => $description,
            'user_id' => $user_id
        ];
        foreach ($data as $field => $value) {
            if (empty($value)) {
                throw new InvalidArgumentException("$field est requis");
            }
        }
    }
}

?>