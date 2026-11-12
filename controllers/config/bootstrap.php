<?php
// Carga explícita de las capas, como en el ejemplo del curso.
$root = dirname(__DIR__, 2);
foreach ([
    'models/database/retro_db_interface.php', 'models/database/retro_db.php',
    'models/entities/sprint.php', 'models/entities/retro_item.php', 'models/mappers/retro_mappers.php',
    'models/repositories/retro_repository/retro_repository_interface.php',
    'models/repositories/retro_repository/retro_repository.php', 'models/config/models_config.php',
    'controllers/mappers/sprints_mapper_controller.php',
    'controllers/retro/retro_controller_interface.php', 'controllers/retro/retro_controller.php',
    'controllers/config/controllers_config.php'
] as $file) require_once $root . '/' . $file;
