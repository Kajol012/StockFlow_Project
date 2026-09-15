<?php
class UserModel {
    public function __construct(private PDO $pdo) {}

    public function find(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT id,username,name,email,role,status,created_at FROM users WHERE id=?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function all(): array { return $this->pdo->query('SELECT id,username,name,email,role,status,created_at FROM users ORDER BY id DESC')->fetchAll(); }
    public function create(array $data): void {
        $stmt = $this->pdo->prepare('INSERT INTO users(username,name,email,password,role,status) VALUES(?,?,?,?,?,?)');
        $stmt->execute([$data['username'],$data['name'],$data['email'],password_hash($data['password'], PASSWORD_DEFAULT),$data['role'],$data['status']]);
    }
    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare('UPDATE users SET username=?,name=?,email=?,role=?,status=? WHERE id=?');
        $stmt->execute([$data['username'],$data['name'],$data['email'],$data['role'],$data['status'],$id]);
    }
    public function delete(int $id): void { $this->pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]); }
    public function findByUsernameAnyStatus(string $username): ?array {
        $stmt=$this->pdo->prepare('SELECT id,username FROM users WHERE username=? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }
    public function findByEmailAnyStatus(string $email): ?array {
        $stmt=$this->pdo->prepare('SELECT id,email FROM users WHERE email=? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    public function findByUsername(string $username): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username=? AND status='Active' LIMIT 1");
        $stmt->execute([$username]); return $stmt->fetch() ?: null;
    }
    public function updateOwnProfile(int $id, string $username, string $name, string $email, ?string $password = null): void {
        if ($password !== null) {
            $stmt=$this->pdo->prepare('UPDATE users SET username=?,name=?,email=?,password=? WHERE id=?');
            $stmt->execute([$username,$name,$email,password_hash($password,PASSWORD_DEFAULT),$id]);
        } else {
            $stmt=$this->pdo->prepare('UPDATE users SET username=?,name=?,email=? WHERE id=?');
            $stmt->execute([$username,$name,$email,$id]);
        }
    }
    public function passwordFor(int $id): ?string { $s=$this->pdo->prepare('SELECT password FROM users WHERE id=?');$s->execute([$id]);return $s->fetchColumn() ?: null; }
}
