<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Task.php';

class UserController
{
    // Gérer l'inscription d'un utilisateur
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['nom'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation de base
            $errors = [];

            if (empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            }

            if (empty($email)) {
                $errors[] = "L'email est requis";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "L'email n'est pas valide";
            }

            if (empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif (strlen($password) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
            }

            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }

            if (empty($errors)) {
                $user = new User();
                $result = $user->register($username, $email, $password);

                if ($result == true) {
                    $_SESSION['username'] = $username;
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['success_message'] = "Inscription réussie! Vous êtes maintenant connecté.";
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = "Une erreur s'est produite lors de l'inscription. L'email est peut-être déjà utilisé.";
                }
            }

            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            include __DIR__ . '/../views/signup.php';
        } else {
            // Afficher le formulaire d'inscription
            include __DIR__ . '/../views/signup.php';
        }
    }

    public function showRegisterForm()
    {
        include __DIR__ . '/../views/signup.php';
    }

    // Gérer la connexion d'un utilisateur
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $errors = [];

            if (empty($email)) {
                $errors[] = "Veuillez saisir votre email ";
            }
            if (empty($password)) {
                $errors[] = "Veuillez saisir votre  mot de passe";
            }

            if (empty($errors)) {
                $user = new User();

                $result = $user->login($email, $password);

                if ($result) {
                    $_SESSION['success_message'] = "Connexion réussie!";
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = "Email ou mot de passe incorrect";
                }
            }

            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            include __DIR__ . '/../views/signin.php';
        } else {
            // Afficher le formulaire de connexion
            $errors[] = "methode d'envoie incorrecte";
            include __DIR__ . '/../views/signin.php';
        }
    }

    public function showLoginForm()
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        include __DIR__ . '/../views/signin.php';
    }

    // Gérer la déconnexion d'un utilisateur
    public function logout()
    {
        User::logout();
        $_SESSION['success_message'] = "Vous avez été déconnecté avec succès";
        header('Location: index.php');
        exit;
    }

    // Afficher le profil de l'utilisateur    

    public function showProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $user = User::getById($_SESSION['user_id']);
        $users = $user;
        include __DIR__ . '/../views/profile.php';
    }

    public function showEditProfileForm()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $user = User::getById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION['error_message'] = "Utilisateur non trouvé";
            header('Location: index.php?action=profile');
            exit;
        }

        include __DIR__ . '/../views/edit-profile.php';
    }



    public function home()
    {
        // Récupérer les taches en vues
        $tasks = Task::getAll();

        // Préparer les données pour la vue
        $data = [
            'pageTitle' => 'Accueil',
            'tasks' => $tasks
        ];

        // Inclure la vue
        include __DIR__ . '/../views/home.php';
    }



    public function deleteAccount()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = User::getById($_SESSION['user_id']);
            if (!$user) {
                $_SESSION['error_message'] = "Utilisateur non trouvé";
                header('Location: index.php?action=profile');
                exit;
            }

            $result = $user->deleteAccount();

            if ($result) {
                // Détruire la session avant la redirection
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['success_message'] = "Votre compte a été supprimé avec succès.";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error_message'] = "Une erreur s'est produite lors de la suppression du compte.";
                header('Location: index.php?action=profile');
                exit;
            }
        } else {
            header('Location: index.php?action=profile');
            exit;
        }
    }
}
?>