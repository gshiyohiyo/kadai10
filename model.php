<?php

function getDetailModel($pdo)
{
    if($pdo != null){
        $id = $_GET['id'];
        // クエリの実行
        $query = "SELECT * FROM benchmarks WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $res = $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row != null)
        {
            $id = $row["id"];
            $platform = $row["platform"];
            $date = $row["date"];
            $cpu = $row["cpu"];
            $system = $row["system"];
            $single = $row["single"];
        
            $view = <<< EOM
            <form method="POST" action="update.php">
                <input type="hidden" name="id" value="{$id}">
                <label for "platform">OS:</label><input type="text" name="platform" value="{$platform}">
                <label for "cpu">CPU: </label><input type="text" name="cpu" value="{$cpu}">
                <label for "system">SYS: </label><input type="text" name="system" value="{$system}">
                <label for "single">Score: </label><input type="text" name="single" value="{$single}">
                <button type="submit">更新</button>
            </form>
            EOM;
            return $view;
        }
    }
}

function getReadMenu()
{
    if($_SESSION["kanri_flg"] == 1)
    {
        $menu = '<a href="logout.php">[ログアウト]</a> ';
    }
    else
    {
        $menu = '<a href="login.php">[ログイン]</a> ';
    }

    return $menu;
}

function getReadModel($pdo)
{
    if($pdo != null){    
        $view = "";
        // クエリの実行
        $query = "SELECT * FROM benchmarks";
        $stmt = $pdo->query($query);

        // 表示処理
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $id = $row["id"];
            $platform = $row["platform"];
            $date = $row["date"];
            $cpu = $row["cpu"];
            $system = $row["system"];
            $single = $row["single"];

            $edit = "";
            if($_SESSION["kanri_flg"] == 1)
            {
                $edit = <<< EOM
                <a href="detail.php?id={$id}">[編集]</a>
                <a href="delete.php?id={$id}">[削除]</a>
                EOM;
            }
        
            $view .= <<< EOM
            <div class="grid">
                <div class="id">ID: {$id}
                $edit 
                </div>
                <div class="platform">OS: {$platform}</div>
                <div class="date">{$date}</div>
                <div class="cpu">CPU: {$cpu}</div>
                <div class="system">SYS: {$system}</div>
                <div class="single">{$single}</div>
            </div>
            EOM;
        }

        return $view;
    }
}

// ベンチマーク結果のクラス
class BenchmarkResult
{
    public $id;
    public $platform;
    public $date;
    public $cpu;
    public $system;
    public $single;

    public function check()
    {
        if(empty($this->platform) || empty($this->cpu) || empty($this->system) || empty($this->single))
        {
            return false;
        }
        return true;
    }

    public function __construct()
    {
        $this->id = 0;
        $this->platform = "";
        $this->date = "";
        $this->cpu = "";
        $this->system = "";
        $this->single = 0;
    }

    public function __destruct()
    {
    }
}

function getUpdateModel($pdo)
{
    $br = new BenchmarkResult();

    if(empty($_POST['result'])) {
        $br->id = h($_POST['id']);
        $br->platform = h($_POST["platform"]);
        $br->date = h($_POST["date"]);
        $br->cpu = h($_POST["cpu"]);
        $br->system = h($_POST["system"]);
        $br->single = h($_POST["single"]);
        
        // 空チェックしているだけ・・・
        $emptyFlag = $br->check();
    }

    $pdo = connectDB();
    if($pdo != null){
    
        // クエリの実行
        $sql = "UPDATE benchmarks 
        SET platform=:platform, cpu=:cpu, system=:system, single=:single, date=sysdate()
        WHERE id=:id;";
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $br->id, PDO::PARAM_INT);
        $stmt->bindValue(':platform', $br->platform, PDO::PARAM_STR);
        $stmt->bindValue(':cpu', $br->cpu, PDO::PARAM_STR);
        $stmt->bindValue(':system', $br->system, PDO::PARAM_STR);
        $stmt->bindValue(':single', $br->single, PDO::PARAM_INT);

        $status = $stmt->execute();

    }
    disconnectDB($pdo);

    $view = <<< EOM
    <div class="grid">
        <div class="id">ID: --</div>
        <div class="platform">OS: {$br->platform}</div>
        <div class="date">{$br->date}</div>
        <div class="cpu">CPU: {$br->cpu}</div>
        <div class="system">SYS: {$br->system}</div>
        <div class="single">{$br->single}</div>
    </div>
    EOM;

    return $view;
}

function getWriteModel($pdo)
{
    if(empty($_POST['result'])) {
        $platform = h($_POST["platform"]);
        $date = h($_POST["date"]);
        $cpu = h($_POST["cpu"]);
        $system = h($_POST["system"]);
        $single = h($_POST["single"]);        
    } else {
        $json = json_decode($_POST['result'], true);
        $platform = h($json["platform"]);
        $date = h($json["date"]);
        $cpu = h($json["cpu"]);
        $system = h($json["system"]);
        $single = h($json["single"]);
    }

    $pdo = connectDB();
    if($pdo != null){
    
        // クエリの実行
        $sql = "INSERT INTO benchmarks (id, platform, cpu, system, single, date) VALUES (NULL, :platform, :cpu, :system, :single, sysdate())";
        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':platform', $platform, PDO::PARAM_STR);
        $stmt->bindValue(':cpu', $cpu, PDO::PARAM_STR);
        $stmt->bindValue(':system', $system, PDO::PARAM_STR);
        $stmt->bindValue(':single', $single, PDO::PARAM_INT);

        $stmt->execute();
    }
    disconnectDB($pdo);

    $view = <<< EOM
    <div class="grid">
        <div class="id">ID: --</div>
        <div class="platform">OS: {$platform}</div>
        <div class="date">{$date}</div>
        <div class="cpu">CPU: {$cpu}</div>
        <div class="system">SYS: {$system}</div>
        <div class="single">{$single}</div>
    </div>
    EOM;

    return $view;
}