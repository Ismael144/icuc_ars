/**
 *  --------------------------------------
 * | The Image Uploader Javascript File   |
 *  -------------------------------------
 */

function validateImages() {
  var fileInput = document.getElementById("fileInput");
  var fileList = fileInput.files;
  console.log(fileList)
  const validFiles = [];
  for (const file of fileList) {
    if (["image/jpeg", "image/png"].includes(file.type)) {
      validFiles.push(file);
    }
  }
  return validFiles;
}

function getNumberOfFiles(validatedImages) {
  const fileNumberBadge = document.getElementById("fileUploadCount");
  // Get the number of files uploaded
  var numberOfFiles = validatedImages.length;

  // Put the number of files in the DOM
  fileNumberBadge.innerHTML = `
            <badge class="badge bg-success">
                ${numberOfFiles} Files Uploaded
            </badge>
        `;
}

function uploadFile(validatedImages) {
  var fileList = validatedImages;

  var validatedImages = validatedImages;

  document.getElementById("fileList").innerHTML = ""

  // Add uploaded files to the list
  for (var i = 0; i < fileList.length; i++) {
    const url = URL.createObjectURL(fileList[i]);
    // const image = document.createElement('img');
    // image.src = url;
    var fileName = fileList[i].name;
    var fileSizeInBytes = fileList[i].size;

    // Convert bytes to kilobytes (1 KB = 1024 bytes)
    var fileSizeInKB = fileSizeInBytes / 1024;

    // Display the file size
    var fileSize = fileSizeInKB.toFixed(2) + " KB";
    var listItem = document.createElement("div");
    listItem.innerHTML = `
                <li class="list-item d-flex justify-content-between gap-2 my-2 border rounded p-2">
                <div class="image-info-group d-flex align-items-center gap-2">
                    <div class="img-case">
                        <img src="${url}" class="rounded" width="60px" style="object-fit: cover; object-position: center; height: 60px;"> 
                    </div>
                    <div class="content">
                        <div class="d-flex flex-column">
                        <h5 class="mb-1">
                            ${fileName}
                        </h5>
                        <span>
                            <p class="fs-sm text-muted mb-0"><strong>${fileSize}</strong></p>
                        </span>
                        </div>
                    </div>
                    </div>
                </li>
    `;

    document.getElementById("fileList").appendChild(listItem);
  }

  // You can also perform additional actions here, such as sending files to the server
  // for processing or storage.
}

function carryOutOperations() {
  const validateduploadedImages = validateImages();
  uploadFile(validateduploadedImages);
}
