<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/assets/js/memo.js?v=<?=time()?>"></script>
    <link rel="stylesheet" href="/assets/css/memo.css?v=<?=time()?>">
</head>
    <body>
        <div class="wrap">
            <div class="nav">
                <div class="contextmenu_box">

                </div>
                <ul class="nav_contextmenu" name="contextmenu">
                    <li><a href="#" class="add_folder">New Folder</a></li>
                </ul>
                <div class="nav_header">
                    <span>FOLDERS</span>
                </div>
                <div class="nav_contents" id="nav_contents">
                    <ul>
                        <?php foreach ($folderList->main as $folder): ?>
                            <li name="folder" id="folder-<?=$folder->seq?>" data-folder-seq="<?=$folder->seq?>">
                                <span id="folder-close" class='close'>▶</span>
                                <span class="folder_name"><?=$folder->name?></span>

                                <ul name="file-ul">
                                </ul>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>

            <div class="main">
                <div class="main_header">
                </div>
                <div class="main_contents">
                    <textarea class="source" spellcheck="false"></textarea>
                </div>
            </div>
        </div>
    </body>
</html>
<script>
    var folderListSub = JSON.parse(<?=var_export($folderList->sub)?>);
    var fileList = JSON.parse(<?=var_export($fileList)?>);
</script>
