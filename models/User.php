<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Task.php';

class User
{
    private $id;
    private $username;
    private $email;
    private $password;


    // Getter pour l'ID utilisateur
    public function getId()
    {
        return $this->id;
    }
    // Getter pour l'email
    public function getEmail()
    {
        return $this->email;
    }


    // Getter pour le nom d'utilisateur
    public function getUsername()
    {
        return $this->username;
    }

    // Inscription d'un utilisateur
    public function register($username, $email, $password)
    {
        $conn = connectDB();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (nom, email, password) 
                                   VALUES (:nom, :email, :password)");
            $stmt->bindParam(':nom', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();
            $this->id = $conn->lastInsertId();
            $this->username = $username;
            $this->email = $email;

            return true;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();

            return false;
        }
    }

    // Connexion d'un utilisateur
    public function login($email, $password)
    {
        $conn = connectDB();

        try {
            $stmt = $conn->prepare("SELECT id, nom, email, password FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($password, $user['password'])) {
                    $this->id = $user['id'];
                    $this->username = $user['nom'];
                    $this->email = $user['email'];

                    $_SESSION['user_id'] = $this->id;
                    $_SESSION['username'] = $this->username;

                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Récupérer un utilisateur par son ID
    public static function getById($id)
    {
        $conn = connectDB();

        try {
            $stmt = $conn->prepare("SELECT id, nom, email FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                $user = new User();
                $user->id = $userData['id'];
                $user->username = $userData['nom'];
                $user->email = $userData['email'];


                return $user;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Récupérer les taches créés par l'utilisateur
    public function getTasks()
    {
        $conn = connectDB();

        try {
            $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }


    /**
     * recuperer le createur d'une tache en fonction de l'id de la tache
     * @param $id_task l'identifiant de la tache
     * @return mixed un tableau d'utilisateur
     */
    public function getUserByIdTasks($id_task)
    {
        $conn = connectDB();
        try {
            $stmt = $conn->prepare('SELECT *  FROM users u
                                    JOIN tasks t ON u.id = t.user_id
                                    WHERE t.id = :id_task');
            $stmt->bindParam(':id_task', $id_task);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Vérifier le mot de passe de l'utilisateur
    public function verifyPassword($userId, $password)
    {
        $conn = connectDB();

        try {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return password_verify($password, $user['password']);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }


    // Déconnexion de l'utilisateur
    public static function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return true;
    }

    public function deleteAccount()
    {
        $conn = connectDB();
        try {

            // Supprimer les taches de l'utilisateur
            $stmt = $conn->prepare("DELETE FROM tasks WHERE user_id = :id");
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            // Enfin, supprimer l'utilisateur
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    //recuperer un utilisateur en fonction de son email
    public function getUserByEmail($email)
    {
        $conn = connectDB();
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email=:email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            } else {
                return [];
            }
        } catch (PDOException $e) {
            return [];
        }
    }

}

?>