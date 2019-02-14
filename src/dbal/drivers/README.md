Info for unbuffered statements in postgre

```php
global $pdo = new PDO('pgsql:'); // Just for reference, I usually use a class to wrapper all this

function fetchCursor($sql, $idCol = false) {
  /*
  nextCursorId() is an undefined function, but
  the objective of it is to create a unique Id for each cursor.
 */
  try {
    $cursorID = nextCursorId();
    $pdo->exec("DECLARE {$cursorID} NO SCROLL FOR ({$sql})");
    $stm = $pdo->prepare('FETCH NEXT FROM {$cursorID}');

    if ($stm) {
      while ($row = $stm->fetch(PDO::FETCH_ASSOC) {
        if (is_string($idCol) && array_key_exists($idCol, $row)) {
          yield $row[$idCol] => $row;
        } else {
          yield $row;
        }
      }
    }
  } catch (Exception $ex) {
    // Anything you want
  } finally {
    /*
    Do some clean up after the loop is done.
    This is in a "finally" block because if you break the parent loop, it still gets called.
    */
    $pdo->exec("CLOSE {$cursorID}");
    return;
  }
}
```