<?php
/**
 * Class ProjectsController
 * @author  ARSENE TEKEU
 * @version 1.0
 * @date    Mai 2025
 */
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Task.php";


class TaskControllers
{

    /**
     * Afficher la liste des taches
     * Summary of index
     * @return void
     */
    public function index()
    {
        try {
            $id = $_GET['id'] ?? 0;
            $tasks = Task::getAll();


            include __DIR__ . '/../views/tasks.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $tasks = [];
            include __DIR__ . '/../views/tasks.php';
        }

    }
    /**
     * Afficher les détails d'un task
     * Summary of show
     * @return void
     */
    public function show()
    {
        $id = $_GET['id'] ?? 0;

        $task = new Task();
        $taskDetails = $task->getTasksById($id);

        if (!$taskDetails) {
            $_SESSION['error_message'] = "La tache demandé n'existe pas";
            header('Location: index.php');
            exit;
        }
        include __DIR__ . '/../views/task-details.php';
    }
    /**
     * Afficher le formulaire de création de tache
     */

    public function showCreateTaskForm()
    {
        include __DIR__ . '/../views/create-task.php';
    }

    /**
     * Traiter la soumission du formulaire de création de task
     * Summary of create
     * @return void
     */
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour créer une tache";
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $user_id = $_SESSION['user_id'];

            $errors = [];

            if (empty($titre)) {
                $errors[] = "Le titre est requis";
            }
            if (strlen($titre) > 255) {
                $errors[] = "Titre trop longue !";
            }
            if (empty($description)) {
                $errors[] = "La description est requise";
            }
            if (strlen($description) > 255) {
                $errors[] = "Description trop longue !";
            }

            if (empty($errors)) {
                $project = new Task();
                $result = $project->createTask($titre, $description, $user_id);
                if ($result) {
                    $_SESSION['success'] = "Votre tache a été créé avec succès";
                    header('Location: index.php?action=create_task');
                    exit;
                } else {
                    $errors[] = "Une erreur s'est produite lors de la création du task";
                }
            }

            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            include __DIR__ . '/../views/create-task.php';
        } else {
            header('Location: index.php?action=create_task');
            exit;
        }
    }

    /**
     * Afficher le formulaire de modification d'une tache
     * @return void
     */
    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour modifier un task";
            header('Location: index.php?action=login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        $task = new Task();
        $taskDetails = $task->getTasksById($id);

        if (!$taskDetails) {
            $_SESSION['error_message'] = "La tache demandé n'existe pas";
            header('Location: index.php');
            exit;
        }
        // Vérifier que l'utilisateur est le créateur du tache
        if ($taskDetails['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Vous n'êtes pas autorisé à modifier cette task";
            header('Location: index.php');
            exit;
        }
        include __DIR__ . '/../views/edit-task.php';
    }

    /**
     * Traiter la soumission du formulaire de modification de task
     * Summary of update
     * @return void
     */
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Vous devez être connecté pour modifier une tache";
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $user_id = $_POST['user_id'] ?? $_SESSION['user_id'];

            $task = new Task();
            $taskDetails = $task->getTasksById($id);

            if (!$taskDetails) {
                $_SESSION['error_message'] = "La tache demandée n'existe pas";
                header('Location: index.php');
                exit;
            }

            // Vérifier que l'utilisateur est le créateur du tache
            if ($taskDetails['user_id'] != $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Vous n'êtes pas autorisé à modifier cette tache";
                header('Location: index.php');
                exit;
            }

            $errors = [];

            if (empty($titre)) {
                $errors[] = "Le titre est requis";
            }

            if (empty($description)) {
                $errors[] = "La description est requise";
            }


            if (empty($errors)) {
                $result = $task->updateTask($id, $titre, $description, $user_id);

                if ($result) {
                    $_SESSION['success'] = "Votre tache a été mis à jour avec succès";
                    header('Location: index.php?action=project&id=' . $id);
                    exit;
                } else {
                    $errors[] = "Une erreur s'est produite lors de la mise à jour de la tache";
                }
            }

            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            include __DIR__ . '/../views/edit-task.php';
        } else {
            header('Location: index.php');
            exit;
        }
    }

    // Supprimer un task
    /**
     * Supprimer un task
     * @return void
     */
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $taskId = $_POST['taskId_id'] ?? null;

            if (!$taskId) {
                $_SESSION['error_message'] = "ID de tache manquant.";
                header('Location: index.php?action=profile');
                exit;
            }

            $task = new Task();
            $taskDetails = $task->getTasksById($taskId);

            if (!$taskDetails) {
                $_SESSION['error_message'] = "task non trouvé.";
                header('Location: index.php?action=profile');
                exit;
            }

            // Vérifier que l'utilisateur est le créateur du task
            if ($taskDetails['user_id'] != $_SESSION['user_id']) {
                $_SESSION['error_message'] = "Vous n'êtes pas autorisé à supprimer ce task.";
                header('Location: index.php?action=profile');
                exit;
            }


            // Supprimer  la tache
            $result = $task->deleteTask($taskId);

            if ($result) {
                $_SESSION['success_message'] = "La tache a été supprimé avec succès.";
            } else {
                $_SESSION['error_message'] = "Une erreur s'est produite lors de la suppression de la tache.";
            }

            header('Location: index.php?action=profile');
            exit;

        } else {
            $_SESSION['error_message'] = "Méthode de requête invalide.";
            header('Location: index.php?action=profile');
            exit;
        }
    }
    /**
     * Afficher les détails d'une tache
     * @param int $id L'ID du task
     * @return void 
     */
    public function showtaskDetails(int $id)
    {
        $task = new Task();
        $users = new User();
        $user = $users->getUserByIdTasks($id);
        $taskDetails = $task->getTasksById($id);

        include __DIR__ . '/../views/project-details.php';
    }
    //formulaire de creation des taches
    public function showCreatetaskDetails()
    {
        include __DIR__ . '/../views/create-project.php';
    }


}



?>