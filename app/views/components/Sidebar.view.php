<div class="sidebar">
    <?php

    $employee = new App\Models\Employee();
    $permissionCategories = $employee->getPermissionCategories();

    foreach ($permissionCategories as $permissionCategory) {
        $path = "/" . strtolower($permissionCategory);
        $isSelected = $_SERVER["REDIRECT_URL"] == $path;

        echo "<a ";
        echo !$isSelected ? "href=\"$path\"" : "";
        echo " class=\"sidebar-item";
        echo $isSelected ? " selected" : "";
        echo "\">$permissionCategory</a>";
    }

    ?>
</div>