
function FileProgressDy(file, targetID,swfUploadInstance){
	this.fileProgressID = file.id;
	this.opacity = 100;
	this.height = 0;
	this.fileProgressElement = document.getElementById(this.fileProgressID);;
	if (!this.fileProgressElement) {
		var disp = '<li id="'+this.fileProgressID+'"  ><div class="inner">'+file.name+'';
				disp +='   <span class="tips">正在上传...</span></div>';
				 disp +='  <span class="close ico-close-btn" >"'+this.fileProgressID+'" </span></li>';
		$("#"+targetID).append(disp);
		
		this.fileProgressWrapper = $("#"+this.fileProgressID+" .ico-close-btn");
		this.fileProgressWrapper.click( function () { $(this).hide();swfUploadInstance.cancelUpload(file.id); });
	}
}
function FileDisplayHide(id){
	var oSelf = $("#"+id).delay(500).hide(500);
}

function FileProgressDySuccess(file, targetID,swfUploadInstance){
	this.fileProgressID = file.id;
	this.opacity = 100;
	this.height = 0;
	this.fileProgressElement = document.getElementById(this.fileProgressID);;
	
		var disp = '<div class="pic">';
				disp +=' <img class="img" src=""></div>';
				disp +='<div class="box"><input type="hidden" name="upfiles_id[]" value=""></div><span class="move ico-move"></span>';
				 disp +='  <span class="close ico-close-btn">删除</span>';
				 $("#"+this.fileProgressID).addClass("fin");
		$("#"+this.fileProgressID).html(disp);
		
		this.fileProgressWrapper = $("#"+this.fileProgressID+" .ico-close-btn");
		this.fileProgressWrapper.click( function () { $(this).parent().remove(); });
	
}

   
function fileQueued(file) {
	try {
		var progress = new FileProgressDy(file, this.customSettings.progressTarget,this);
		$("#"+file.id+" .tips").html("准备中...");
	} catch (ex) {
		this.debug(ex);
	}

}

function fileQueueError(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		var progress = new FileProgress(file, this.customSettings.progressTarget);
		progress.setError();
		progress.toggleCancel(false);

		switch (errorCode) {
		case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			progress.setStatus("File is too big.");
			this.debug("Error Code: File too big, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			progress.setStatus("Cannot upload Zero Byte files.");
			this.debug("Error Code: Zero byte file, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			progress.setStatus("Invalid File Type.");
			this.debug("Error Code: Invalid File Type, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		default:
			if (file !== null) {
				progress.setStatus("Unhandled Error");
			}
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
		var progress = new FileProgressDy(file, this.customSettings.progressTarget,this);
		$("#"+file.id+" .tips").html("正在上传中...");
		
	}
	catch (ex) {}
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		var progress = new FileProgressDy(file, this.customSettings.progressTarget,this);
		$("#"+file.id+" .tips").html("上传中，请等待...");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
	try {
		var progress = new FileProgressDySuccess(file, this.customSettings.progressTarget,this);
		//$("#"+file.id+" .tips").html("上传完成");
		var data = serverData;
		  jsonData=eval("("+serverData+")"); 
		if (jsonData['url'] !== "") {
			$("#"+file.id+" .img").attr("src",jsonData['url']);
			$("#"+file.id+" input").val(jsonData['id']);

		} else {
			//addImage("images/error.gif");
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadError(file, errorCode, message) {
	try {
		var progress = new FileProgressDy(file, this.customSettings.progressTarget,this);
		
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			progress.setStatus("Upload Error: " + message);
			this.debug("Error Code: HTTP Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			progress.setStatus("Upload Failed.");
			this.debug("Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			progress.setStatus("Server (IO) Error");
			this.debug("Error Code: IO Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			progress.setStatus("Security Error");
			this.debug("Error Code: Security Error, File name: " + file.name + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			progress.setStatus("Upload limit exceeded.");
			this.debug("Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			progress.setStatus("Failed Validation.  Upload skipped.");
			this.debug("Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			// If there aren't any files left (they were all cancelled) disable the cancel button
			$("#"+file.id+" .tips").html("已取消");;
			FileDisplayHide(file.id);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			progress.setStatus("Stopped");
			break;
		default:
			progress.setStatus("Unhandled Error: " + errorCode);
			this.debug("Error Code: " + errorCode + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message);
			break;
		}
	} catch (ex) {
        this.debug(ex);
    }
}



function addImage(src) {
	var newImg = document.createElement("img");
	newImg.style.margin = "5px";

	document.getElementById("thumbnails").appendChild(newImg);
	if (newImg.filters) {
		try {
			newImg.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			newImg.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		newImg.style.opacity = 0;
	}

	newImg.onload = function () {
		fadeIn(newImg, 0);
	};
	newImg.src = src;
}

