<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <style>
        ul {list-style: none; margin:0px; padding:0px;}

        .wrap {width: 100%;height: 100%;}
        .nav {width: 20%;height: 100%;float: left;background: #181836;}
        .main {width: 100%;height: 100%;background: #262626;}

        .nav_header {height: 5%;margin: 1% 0 0 2%}
        .nav_header span {color: white;font-weight: bold;font-size: 15px;}

        .nav_contents {color: white;margin: 0 0 0 2%;}
        .nav_contents ul li {cursor: pointer;font-size: 13px;}

        [name="file"] {padding-left: 10%;}
        [name="file-ul"] {display: none;}
    </style>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
    <body>
        <div class="wrap">
            <div class="nav">
                <div class="nav_header">
                    <span>FOLDERS</span>
                </div>
                <div class="nav_contents" id="nav_contents">
                    <ul>
                        <?php foreach ($folderList as $folder): ?>
                            <li name="folder" id="folder-<?=$folder->seq?>" data-folder-seq="<?=$folder->seq?>">
                                <span id="folder-close" class='close'>▶</span>
                                <span class="folder_name"><?=$folder->name?></span>
                                <span class="add_folder">+</span>
                                <span class="remove_folder">-</span>

                                <ul name="file-ul">
                                    <?php foreach ($fileList as $file): ?>
                                        <?php if ($file->parent_id == $folder->seq): ?>
                                            <li name="file" id="file-<?=$file->seq?>" data-file-seq="<?=$file->seq?>">
                                                <span class="file_name"><?=$file->name?></span>
                                                <span class="remove_file">-</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach ?>
                                </ul>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div class="main">

            </div>
        </div>
    </body>
</html>

<script>
    var folder = $('[name="folder"]'),
        file = $('[name="file"]');

    var rightClickBox = false;

    //기본 브라우져 오른쪽 마우스 클릭 막기
    folder.on('contextmenu', function() {
        return false;
    });

    folder.find('span:eq(0), span:eq(1)').on('click', function(e) {
        var target = $(e.target),
            status = target.parent().find(' > span#folder-close'),
            statusValue = status.attr('class');

        if (statusValue == 'close') {
            status.text('▼');
            status.attr('class', 'open');
            target.parent().find('ul').slideDown();
        } else {
            status.text('▶');
            status.attr('class', 'close');
            target.parent().find('ul').slideUp();
        }
    });

    //파일 추가
    $(document).on('click', '.add_folder', function(e) {
        var target = $(e.target),
            file_name = prompt('파일명을 입력해주세요.');
        if (file_name != null || file_name != '' || file_name.length != 0) {
            $.ajax({
                url : 'memo/addFile',
                data : {
                    parent_id : target.parent().data('folder-seq'),
                    file_name : file_name,
                },
                dataType : 'json',
                type : 'post',
                success : function(json) {
                    var insertTag = "<li name='file' id='file-'" + json.seq + " data-file-seq='" + json.seq + "'><span>" + json.name + "</span> <span class='remove_file'>-</span></li>";
                    target.parent().find('ul[name="file-ul"]').append(insertTag);
                }
            });
        };
    });

    //파일 삭제
    $(document).on('click', '.remove_file', function(e) {
        var target = $(e.target),
            li = target.parent();

        if (confirm(li.find('.file_name').text() + ' 파일을 삭제하시겠습니까?')) {
            $.ajax({
                url : 'memo/removeFile',
                data : {
                    seq : target.parent().data('file-seq'),
                },
                dataType : 'text',
                type : 'post',
                success : function(result) {
                    if (result) {
                        li.remove();
                    } else {
                        alert('삭제 오류');
                    }
                }
            })
        }
    });

</script>
