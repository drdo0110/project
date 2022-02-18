<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <style>
        ul {list-style: none; margin:0px; padding:0px;}

        .wrap {width: 100%;height: 100%;}
        .nav {width: 20%;height: 100%;float: left;background: #181836;}
        .main {width: 80%;height: 100%;float: right;background: #262626;color: white}

        .nav_header {height: 5%;margin: 1% 0 0 2%}
        .nav_header span {color: white;font-weight: bold;font-size: 15px;}

        .nav_contents {color: white;margin: 0 0 0 2%;}
        .nav_contents ul li {cursor: pointer;font-size: 13px;}

        [name="file"] {padding-left: 10%;}
        [name="file-ul"] {display: none;}

        .main_header {height: 4%; background: #3c3c3c;}
        .main_contents{height: 96%;}
        .main_contents textarea {width: 100%;height: 100%;background: #262626;color: white;border: none;outline: none; font-size: 14px;}

        .click_file_name {font-size : 12px;float:left;cursor:pointer;padding: 10px;}
        .click_file_name.active {background: #262626; border-bottom: 1px solid #262626;}
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
    let folder = $('[name="folder"]'),
        file = $('[name="file"]');

    let rightClickBox = false;

    //기본 브라우져 오른쪽 마우스 클릭 막기
    folder.on('contextmenu', function() {
        return false;
    });

    //폴더 열닫
    folder.find('span:eq(0), span:eq(1)').on('click', function(e) {
        let target = $(e.target),
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
        let target = $(e.target),
            file_name = prompt('파일명을 입력해주세요.');
        if (file_name != null || file_name != '' || file_name.length != 0) {
            $.ajax({
                url : 'memo/addFile',
                data : {
                    parentId : target.parent().data('folder-seq'),
                    fileName : file_name,
                },
                dataType : 'json',
                type : 'post',
                success : function(json) {
                    if (json.status) {
                        let insertTag = `
                            <li name='file' id='file-${json.seq}' data-file-seq='${json.seq}'>
                                <span class='file_name'>${json.name}</span>
                                <span class='remove_file'>-</span>
                            </li>
                        `;

                        target.parent().find('ul[name="file-ul"]').append(insertTag);

                        commonLoadFile(json.seq, 'detail');
                    } else {
                        alert(json.msg);
                    }
                }
            });
        };
    });

    //파일 삭제
    $(document).on('click', '.remove_file', function(e) {
        let target = $(e.target),
            li = target.parent(),
            seq = target.parent().data('file-seq');
        console.log(target);
        if (confirm(li.find('.file_name').text() + ' 파일을 삭제하시겠습니까?')) {
            $.ajax({
                url : 'memo/removeFile',
                data : {
                    seq : seq,
                },
                dataType : 'text',
                type : 'post',
                success : function(result) {
                    if (result) {
                        li.remove();

                        $(`.click_file_name[data-file-seq="${seq}"]`).remove();

                        let leftSeq = $('.click_file_name').eq($('.click_file_name').length - 1).data('file-seq');
                        commonLoadFile(leftSeq);
                    } else {
                        alert('삭제 오류');
                    }
                }
            })
        }
    });

    //파일 클릭
    $(document).on('click', "[name='file']", function(e) {
        let target = $(e.target),
            li = target.parent(),
            seq = target.parent().data('file-seq');

        //이미 동일한 파일이 열려있으면 return
        var is_file_open = false;
        $.each($('.main_header').find('.click_file_name'), function(idx, el) {
            if (seq == $(el).data('file-seq')) {
                is_file_open = true;
            }
        })

        if (is_file_open) {
            commonLoadFile(seq);
            return;
        }

        commonLoadFile(seq, 'detail');
    });

    //파일 닫기
    $(document).on('click', '.cancel', function(e) {
        let target = $(e.target);
        target.parent().remove();

        let leftSeq = $('.click_file_name').eq($('.click_file_name').length - 1).data('file-seq');
        if (leftSeq != null && leftSeq != '') {
            commonLoadFile(leftSeq);
        } else {
            $('.source').empty();
        }
    })

    //열린 파일 리스트중의 특정 파일 선택시
    $(document).on('click', '.click_file_name', function(e) {
        let target = $(e.target);
        commonLoadFile(target.data('file-seq'));
    })

    //파일 row 호출 - 공통
    function commonLoadFile(seq, type = null) {
        $.ajax({
            url : 'memo/loadFileRow',
                data : {
                    seq : seq,
                },
                dataType : 'json',
                type : 'get',
                success : function(json) {
                    let data = json;
                    $('.source').empty();
                    $('.source').text(data.source);

                    if (type == 'detail') {
                        var div = document.createElement('div');
                        div.className = 'click_file_name';
                        div.innerText = data.name;
                        div.setAttribute('data-file-seq', data.seq);
                        $('.main_header').append($(div));

                        var span = document.createElement('span');
                        span.className = 'cancel';
                        span.innerText = 'x';
                        span.style = 'margin:0 10px 0 10px';
                        $(div).append($(span));
                    }

                    //선택된 파일 active
                    $('.click_file_name').removeClass('active');
                    $.each($('.click_file_name'), function(idx, el) {
                        if (seq == $(el).data('file-seq')) {
                            $(el).addClass('active');
                        }
                    });
                }
        });
    }

</script>
