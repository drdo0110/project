$(document).ready(function(){
    let folder = $('[name="folder"]'),
        file = $('[name="file"]');

    //db에 이미 저장되어있는 폴더 insertAfter
    let existingFolder = folderListSub;
    $.each(existingFolder, function(idx, el) {
        var el = $(el);
        var insertTag = `
            <ul name="folder-ul" style="padding-left: 5%;">
                <li name="folder" id="folder-${el[0].folderSeq}" data-folder-seq="${el[0].folderSeq}">
                    <span id="folder-close" class='close'>▶</span>
                    <span class="folder_name">${el[0].folderName}</span>
                    <ul name="file-ul">
                    </ul>
                </li>
            </ul>
        `;

        $(insertTag).insertAfter(`[name="folder"][data-folder-seq="${el[0].folderParentId}"] > span.folder_name`);
    });

    let existingFile = fileList;
    $.each(existingFile, function(idx, el) {
        var el = $(el);
        var insertTag = `
            <li name='file' id='file-${el[0].seq}' data-file-seq='${el[0].seq}'>
                <span class='file_name'>${el[0].name}</span>
            </li>
        `;

        $(`[name="folder"][data-folder-seq="${el[0].parent_id}"] > ul[name="file-ul"]`).append(insertTag);
    });


    //폴더 contextmenu
    commonContextMenu(folder);

    //nav contextmenu
    $('.nav').on('contextmenu', function(e) {
        let winWidth = $(document).width();
        let winHeight = $(document).height();

        let posX = e.pageX;
        let posY = e.pageY;

        let menuWidth = $(".nav_contextmenu").width();
        let menuHeight = $(".nav_contextmenu").height();

        let secMargin = 10;

        if (posX + menuWidth + secMargin >= winWidth && posY + menuHeight + secMargin >= winHeight) {
            //Case 1: right-bottom overflow:
            posLeft = posX - menuWidth - secMargin + "px";
            posTop = posY - menuHeight - secMargin + "px";

        } else if (posX + menuWidth + secMargin >= winWidth){
            //Case 2: right overflow:
            posLeft = posX - menuWidth - secMargin + "px";
            posTop = posY + secMargin + "px";

        } else if (posY + menuHeight + secMargin >= winHeight){
            //Case 3: bottom overflow:
            posLeft = posX + secMargin + "px";
            posTop = posY - menuHeight - secMargin + "px";

        } else {
            //Case 4: default values:
            posLeft = posX + secMargin + "px";
            posTop = posY + secMargin + "px";
        };

        $(".nav_contextmenu").css({
            "left": posLeft,
            "top": posTop
        }).show();

        $(".folder_contextmenu, .file_contextmenu").hide();
        return false;
    });

    //contextmenu 숨기기
    $(document).click(function(){
        $("[name='contextmenu']").hide();
    });

    //폴더 열닫
    $(document).on('click', 'span#folder-close, span.folder_name', function(e) {
        let target = $(e.target),
            status = target.parent().find(' > span#folder-close'),
            statusValue = status.attr('class');

        if (statusValue == 'close') {
            status.text('▼');
            status.attr('class', 'open');
            target.parent().find(' > ul').slideDown();
        } else {
            status.text('▶');
            status.attr('class', 'close');
            target.parent().find(' > ul').slideUp();
        }
    });

    //폴더 추가
    $(document).on('click', '.add_folder', function(e) {
        let target = $(e.target),
            folderName = prompt('폴더명을 입력해주세요.'),
            folderSeq = target.parents('ul').data('folder-seq');

        if (folderName == '') {
            alert('폴더명이 입력되지 않았습니다.\n다시 확인해주세요.');
            return false;
        }

        if (folderName == null) {
            return false;
        }

        $.ajax({
            url : 'memo/addFolder',
            data : {
                parentId : folderSeq,
                folderName : folderName,
            },
            dataType : 'json',
            type : 'post',
            success : function(json) {
                if (json.status) {
                    if (typeof folderSeq != 'undefined') {
                        let insertTag = `
                            <ul name="folder-ul" style="padding-left: 5%;display: block;">
                                <li name="folder" id="folder-${json.seq}" data-folder-seq="${json.seq}">
                                    <span id="folder-close" class='close'>▶</span>
                                    <span class="folder_name">${json.name}</span>
                                    <ul name="file-ul">
                                    </ul>
                                </li>
                            </ul>
                        `;
                        $(insertTag).insertAfter(`[name="folder"][data-folder-seq="${folderSeq}"] > span.folder_name`);
                    } else {
                        let insertTag = `
                            <li name="folder" id="folder-${json.seq}" data-folder-seq="${json.seq}">
                                <span id="folder-close" class='close'>▶</span>
                                <span class="folder_name">${json.name}</span>
                                <ul name="file-ul">
                                </ul>
                            </li>
                        `;
                        $('.nav_contents > ul').append(insertTag);
                    }

                    commonLoadFolder(json.seq);
                    commonContextMenu($(`#folder-${json.seq}`));
                } else {
                    alert(json.msg);
                }
            }
        });
    });

    //폴더 삭제
    $(document).on('click', '.remove_folder', function(e) {
        let target = $(e.target),
            seq = target.parents('ul').data('folder-seq');

        alert('관리자만 삭제가 가능합니다.');
        return;

        if (confirm($(`[name="folder"][data-folder-seq="${seq}"] > span.folder_name`).text().trim() + ' 폴더을 삭제하시겠습니까?')) {
            $.ajax({
                url : 'memo/removeFolder',
                data : {
                    seq : seq,
                },
                dataType : 'text',
                type : 'post',
                success : function(result) {
                    if (result) {
                        $(`[name="folder"][data-folder-seq="${seq}"]`).remove();
                        commonLoadFolder(seq);
                    } else {
                        alert('삭제 오류');
                    }
                }
            })
        }
    });

    //파일 추가
    $(document).on('click', '.add_file', function(e) {
        let target = $(e.target),
            fileName = prompt('파일명을 입력해주세요.'),
            folderSeq = target.parents('ul').data('folder-seq');

        if (fileName == '') {
            alert('파일명이 입력되지 않았습니다.\n다시 확인해주세요.');
            return false;
        }

        if (fileName == null) {
            return false;
        }

        $.ajax({
            url : 'memo/addFile',
            data : {
                parentId : folderSeq,
                fileName : fileName,
            },
            dataType : 'json',
            type : 'post',
            success : function(json) {
                if (json.status) {
                    let insertTag = `
                        <li name='file' id='file-${json.seq}' data-file-seq='${json.seq}'>
                            <span class='file_name'>${json.name}</span>
                        </li>
                    `;

                    $(`[name="folder"][data-folder-seq=${folderSeq}] > ul[name="file-ul"]`).append(insertTag);
                    commonLoadFile(json.seq, 'detail');
                } else {
                    alert(json.msg);
                }
            }
        });
    });

    //파일 삭제
    $(document).on('click', '.remove_file', function(e) {
        let target = $(e.target),
            seq = target.parents('ul').data('file-seq');

        alert('관리자만 삭제가 가능합니다.');
        return;

        if (confirm($(`[name="file"][data-file-seq="${seq}"]`).text().trim() + ' 파일을 삭제하시겠습니까?')) {
            $.ajax({
                url : 'memo/removeFile',
                data : {
                    seq : seq,
                },
                dataType : 'text',
                type : 'post',
                success : function(result) {
                    if (result) {
                        $(`[name="file"][data-file-seq="${seq}"]`).remove();

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
        let isFileOpen = false;
        $.each($('.main_header').find('.click_file_name'), function(idx, el) {
            if (seq == $(el).data('file-seq')) {
                isFileOpen = true;
            }
        })

        if (isFileOpen) {
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
    });

    //폴더 및 파일 이름 변경
    $(document).on('click', '.rename_folder, .rename_file', function(e) {
        let target = $(e.target),
            type = target.attr('class') == 'rename_folder' ? 'folder' : 'file',
            krType = type == 'folder' ? '폴더' : '파일',
            folderSeq = target.parents('ul').data('folder-seq'),
            fileSeq = target.parents('ul').data('file-seq'),
            changeName = prompt(`${krType}명을 입력해주세요.`);

        if (changeName == '') {
            alert('파일명이 입력되지 않았습니다.\n다시 확인해주세요.');
            return false;
        }

        if (changeName == null) {
            return false;
        }

        $.ajax({
            url : 'memo/changeRename',
            data : {
                folderSeq : folderSeq,
                fileSeq : fileSeq,
                type : type,
                changeName : changeName
            },
            dataType : 'json',
            type : 'get',
            success : function(json) {
                alert(json.msg);
                if (json.status) {
                    //업데이트 이후
                    $(`li[data-file-seq="${fileSeq}"] span.file_name`).text(changeName);
                    $(`.click_file_name[data-file-seq="${fileSeq}"]`).text(changeName);
                    $(`.click_file_name[data-file-seq="${fileSeq}"]`).append('<span class="cancel" style="margin: 0px 10px;">x</span>');
                }
            }
        });
    });

    //열린 파일 리스트중의 특정 파일 선택시
    $(document).on('click', '.click_file_name', function(e) {
        let target = $(e.target);
        commonLoadFile(target.data('file-seq'));
    });

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
                    let div = document.createElement('div');
                    div.className = 'click_file_name';
                    div.innerText = data.name;
                    div.setAttribute('data-file-seq', data.seq);
                    $('.main_header').append($(div));

                    let span = document.createElement('span');
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
    };

    //폴더 row 호출 - 공통
    function commonLoadFolder(seq) {
        $.ajax({
            url : 'memo/loadFolderRow',
            data : {
                seq : seq,
            },
            dataType : 'json',
            type : 'get',
            success : function(json) {
                let data = json;
                $('.source').empty();
            }
        });
    };

    function commonContextMenu(dom) {
        //폴더 contextmenu
        dom.on('contextmenu', function(e) {
            let target = $(e.target);

            let winWidth = $(document).width();
            let winHeight = $(document).height();

            let posX = e.pageX;
            let posY = e.pageY;

            let menuWidth = $(".folder_contextmenu").width();
            let menuHeight = $(".folder_contextmenu").height();

            let secMargin = 10;

            if (posX + menuWidth + secMargin >= winWidth && posY + menuHeight + secMargin >= winHeight) {
                //Case 1: right-bottom overflow:
                posLeft = posX - menuWidth - secMargin + "px";
                posTop = posY - menuHeight - secMargin + "px";

            } else if (posX + menuWidth + secMargin >= winWidth){
                //Case 2: right overflow:
                posLeft = posX - menuWidth - secMargin + "px";
                posTop = posY + secMargin + "px";

            } else if (posY + menuHeight + secMargin >= winHeight){
                //Case 3: bottom overflow:
                posLeft = posX + secMargin + "px";
                posTop = posY - menuHeight - secMargin + "px";

            } else {
                //Case 4: default values:
                posLeft = posX + secMargin + "px";
                posTop = posY + secMargin + "px";
            };

            let tag = '';
            if (target.attr('class') == 'file_name') {
                let fileSeq = target.parent().data('file-seq'),
                    folderSeq = target.parent().parent().parent().data('folder-seq');

                tag += `<ul class="file_contextmenu" name="contextmenu" data-file-seq="${fileSeq}" data-folder-seq="${folderSeq}">`;
                    tag += `<li><a href="#" class="rename_file">File Rename</a></li>`;
                    tag += `<li><a href="#" class="remove_file">File Delete</a></li>`;
            } else if (target.attr('class') == 'folder_name') {
                let seq = target.parent().data('folder-seq');

                tag += `<ul class="folder_contextmenu" name="contextmenu" data-folder-seq="${seq}">`;
                    tag += `<li><a href="#" class="add_folder">New Folder</a></li>`;
                    tag += `<li><a href="#" class="add_file">New File</a></li>`;
                    tag += `<li><a href="#" class="rename_folder">Folder Rename</a></li>`;
                    tag += `<li><a href="#" class="remove_folder">Folder Delete</a></li>`;
            }
            tag += `</ul>`;

            $('.contextmenu_box').empty();
            $('.contextmenu_box').append(tag);

            $(".folder_contextmenu, .file_contextmenu").css({
                "left": posLeft,
                "top": posTop
            }).show();

            $(".nav_contextmenu").hide();
            return false;
        });
    }
});
