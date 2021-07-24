<?php

class Todo
{
    
    /**
     * @var PDO
     */
    private $conn;
    private $table = 'todo';

    public $id;
    public $item;
    public $sort;
    public $todoStatu;
    public $addDate;
    public $completDate;
    public $from;
    public $to;
    public $PosFrom;
    public $PosTo;

   
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get Uncompleted tasks
    public function getUncompleted()
    {
        
        $query = 'SELECT * FROM ' . $this->table . ' WHERE  todoStatu=1 ORDER BY sort ASC  ';

      
        $stmt = $this->conn->prepare($query);

       
        $stmt->execute();

        return $stmt;
    }

    // Get Completed tasks
    public function getCompleted()
    {
       
        $query = 'SELECT * FROM ' . $this->table . ' WHERE  todoStatu=2  ORDER BY sort ASC  ';

        
        $stmt = $this->conn->prepare($query);

       
        $stmt->execute();

        return $stmt;
    }

    // Get Single Task
    public function read_one()
    {
        
        $query = 'SELECT  * FROM ' . $this->table . '  WHERE id = ? LIMIT 0,1';

       
        $stmt = $this->conn->prepare($query);

        $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);

       
        $stmt->bindParam(1, $this->id);

       
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

       
        $this->item = $row['item'];
        $this->sort = $row['sort'];
        $this->todoStatu = $row['todoStatu'];
        $this->addDate = $row['addDate'];
        $this->completDate = $row['completDate'];
    }

    // Create Task
    public function create()
    {
       
        if (!empty($this->item)) {
            $query = 'UPDATE ' . $this->table . ' SET sort = sort+1 WHERE todoStatu = 1';

           
            $stmtUpdate = $this->conn->prepare($query);

           
            if ($stmtUpdate->execute()) {
                
                $query = 'INSERT INTO ' . $this->table . ' SET item = :item, todoStatu = :todoStatu, sort = :sort';

               
                $stmt = $this->conn->prepare($query);

              
                $this->item = filter_var($this->item, FILTER_SANITIZE_STRING);
                $this->sort = filter_var(1, FILTER_SANITIZE_NUMBER_INT);
                $this->todoStatu = filter_var(1, FILTER_SANITIZE_NUMBER_INT);

               
                $stmt->bindParam(':item', $this->item);
                $stmt->bindParam(':sort', $this->sort);
                $stmt->bindParam(':todoStatu', $this->todoStatu);
                if ($stmt->execute()) {
                    return true;
                }
            }

        }


        return false;
    }

    // Update Post
    public function update()
    {
        
        $query = 'UPDATE  ' . $this->table . ' SET item=:item WHERE id=:id';
      
        $stmt = $this->conn->prepare($query);
       
        $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);
        $this->item = filter_var($this->item, FILTER_SANITIZE_STRING);
       
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':item', $this->item);

       
        if ($stmt->execute()) {
            return true;
        }


        return false;
    }

    // Delete Post
    public function delete()
    {
        $query = 'SELECT item FROM ' . $this->table . ' WHERE id = :id';

        // Prepare statement
        $stmtFind = $this->conn->prepare($query);

        // Clean data
        $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);
        // Bind data
        $stmtFind->bindParam(':id', $this->id);
        // Execute query
        if ($stmtFind->execute() and $stmtFind->rowCount() > 0) {
            // Create query
            $query = 'UPDATE  ' . $this->table . ' SET sort = sort - 1 WHERE todoStatu = :todoStatu AND sort > :sort ';

            // Prepare statement
            $stmtUpdate = $this->conn->prepare($query);

            // Clean data
            $this->sort = filter_var($this->sort, FILTER_SANITIZE_NUMBER_INT);
            $this->todoStatu = filter_var($this->todoStatu, FILTER_SANITIZE_NUMBER_INT);

            // Bind data
            $stmtUpdate->bindParam(':sort', $this->sort);
            $stmtUpdate->bindParam(':todoStatu', $this->todoStatu);

            // Execute query
            if ($stmtUpdate->execute()) {
                $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

                // Prepare statement
                $stmt = $this->conn->prepare($query);

                // Clean data
                $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);
                // Bind data
                $stmt->bindParam(':id', $this->id);

                // Execute query
                if ($stmt->execute()) {
                    return true;

                }

            }

        }


        return false;
    }


    public function markAsCompleted()
    {
        $query = 'UPDATE ' . $this->table . ' SET sort = sort +1 WHERE todoStatu= :todoStatu';
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        // Clean data
        $this->todoStatu = filter_var($this->todoStatu, FILTER_SANITIZE_NUMBER_INT);
        // Bind data
        $stmt->bindParam(':todoStatu', $this->todoStatu);
        if ($stmt->execute()) {
            // Create query
            $query = 'UPDATE ' . $this->table . ' SET sort = sort - 1 WHERE sort > :sort and todoStatu = 1';
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            // Clean data
            $this->sort = filter_var($this->sort, FILTER_SANITIZE_NUMBER_INT);
            // Bind data
            $stmt->bindParam(':sort', $this->sort);
            if ($stmt->execute()) {
                $query = 'UPDATE  ' . $this->table . ' SET sort=1,todoStatu=:todoStatu,completDate=:completDate WHERE id=:id';
                // Prepare statement
                $stmt = $this->conn->prepare($query);
                // Clean data
                $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);
                $this->todoStatu = filter_var($this->todoStatu, FILTER_SANITIZE_NUMBER_INT);
                $this->completDate = date("Y-m-d H:i:s");
                // Bind data
                $stmt->bindParam(':id', $this->id);
                $stmt->bindParam(':todoStatu', $this->todoStatu);
                $stmt->bindParam(':completDate', $this->completDate);
                if ($stmt->execute()) {
                    return true;
                }
            }
        }


        return false;
    }

    public function markAsUncompleted()
    {
        $query = 'UPDATE ' . $this->table . ' SET sort = sort +1 WHERE todoStatu= :todoStatu';
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        // Clean data
        $this->todoStatu = filter_var($this->todoStatu, FILTER_SANITIZE_NUMBER_INT);
        // Bind data
        $stmt->bindParam(':todoStatu', $this->todoStatu);
        if ($stmt->execute()) {
            // Create query
            $query = 'UPDATE ' . $this->table . ' SET sort = sort - 1 WHERE sort > :sort and todoStatu = 2';
            // Prepare statement
            $stmt = $this->conn->prepare($query);
            // Clean data
            $this->sort = filter_var($this->sort, FILTER_SANITIZE_NUMBER_INT);
            // Bind data
            $stmt->bindParam(':sort', $this->sort);
            if ($stmt->execute()) {
                $query = 'UPDATE  ' . $this->table . ' SET sort=1,todoStatu=:todoStatu,completDate=:completDate WHERE id=:id';
                // Prepare statement
                $stmt = $this->conn->prepare($query);
                // Clean data
                $this->id = filter_var($this->id, FILTER_SANITIZE_NUMBER_INT);
                $this->todoStatu = filter_var($this->todoStatu, FILTER_SANITIZE_NUMBER_INT);
                $this->completDate = '0000-00-00 00:00:00';
                // Bind data
                $stmt->bindParam(':id', $this->id);
                $stmt->bindParam(':todoStatu', $this->todoStatu);
                $stmt->bindParam(':completDate', $this->completDate);
                if ($stmt->execute()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function Sort()
    {
        $query = 'UPDATE ' . $this->table . ' SET sort = CASE id WHEN :from THEN  :posFrom
                  WHEN :to THEN :posTo  END  WHERE id in (:from, :to)';
        // Prepare statement
        $stmt = $this->conn->prepare($query);
        // Clean data
        $this->from = filter_var($this->from, FILTER_SANITIZE_NUMBER_INT);
        $this->to = filter_var($this->to, FILTER_SANITIZE_NUMBER_INT);
        $this->PosFrom = filter_var($this->PosFrom, FILTER_SANITIZE_NUMBER_INT);
        $this->PosTo = filter_var($this->PosTo, FILTER_SANITIZE_NUMBER_INT);
        // Bind data
        $stmt->bindParam(':from', $this->from);
        $stmt->bindParam(':to', $this->to);
        $stmt->bindParam(':posFrom', $this->PosFrom);
        $stmt->bindParam(':posTo', $this->PosTo);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}
