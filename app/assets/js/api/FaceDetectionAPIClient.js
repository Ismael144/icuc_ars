class FaceDetectionAPIClient {
  constructor(url, id) {
    this.url = url;
    this.id = id;
    this.capturedImagesFromWebCam = []
  }

  configure() {
    Webcam.set({
      width: 490,
      height: 330,
      image: 'png',
    })

    Webcam.attach("#webcam-capture");
  }

  closeWebcam() {
    Webcam.reset()
    document.getElementById('captured-images').innerHTML = ''
  }

  captureImage() {
    Webcam.snap((data_uri) => {
      if (this.capturedImagesFromWebCam.length > 3) {
        alert("You can only upload a maximum of 4 images!")
        return this.capturedImagesFromWebCam
      }

      this.capturedImagesFromWebCam.push(data_uri);

      // Generate unique image ID (replace with your logic if needed)
      document.getElementById("captured-images").innerHTML += `<img src="${data_uri}" alt="Captured Image" />`;
    });
  }

  getUploadedImages() {
    if (this.capturedImagesFromWebCam.length === 0) {
      alert("No images captured yet!");
      return;
    }

    const formData = new FormData();
    for (let i = 0; i < this.capturedImagesFromWebCam.length; i++) {
      // Generate filename based on pattern
      const today = new Date();
      const dateString = `${today.getFullYear()}_${today.getMonth() + 1}_${today.getDate()}`;
      const filename = `ICUC-${this.generateImageId()}-` + dateString + '.jpg';

      // Create Blob object from data URI
      const blob = this.dataURItoBlob(this.capturedImagesFromWebCam[i]);

      // Append Blob to FormData with filename
      formData.append(`images[${i}]`, blob, filename);
    }

    return formData;
  }

  // Helper to convert data URI to Blob
  dataURItoBlob(dataURI) {
    const binary = atob(dataURI.replace(/^data:image\/\w+;base64,/, ""));
    const array = [];
    for (let i = 0; i < binary.length; i++) {
      array.push(binary.charCodeAt(i));
    }
    return new Blob([new Uint8Array(array)], { type: 'image/jpeg' });
  }


  // to generate a unique image ID (replace with your implementation)
  generateImageId() {
    const randomString = Math.random().toString(36).substring(2, 15);
    return randomString;
  }

  async sendImagesToBackend(formElement) {
    formElement.onsubmit = async (e) => {
      e.preventDefault();

      function clearImages() {
        var fileUploadForm = document.getElementById("fileUploadForm");
        fileUploadForm.reset();

        carryOutOperations();
      }

      const formData = new FormData(e.target);

      var fileInput = document.getElementById("fileInput");

      const resultsDiv = document.querySelector(".results");

      if (!fileInput.files.length) {
        resultsDiv.innerHTML = "<div class='alert alert-danger'>Please upload some images to proceed</div>"
        return;
      }

      if (fileInput.files.length > 10) {
        resultsDiv.innerHTML = "<div class='alert alert-danger'>Sorry, you can only upload 10 images at a time but you uploaded " + fileInput.files.length + " images.</div>"
        return;
      }

      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger",
        },
        buttonsStyling: false,
      });

      const sweetAlert1 = swalWithBootstrapButtons.fire({
        title: false,
        html: `
            <div class='my-3'>
                <img src='../../assets/images/preloaders/Settings.gif' />
            </div>
            <div class='some-content' style='margin-bottom: -10px;'>
                <h4 class='mb-3'>Processing Images</h4>
                <p class='text-muted' style='font-size: 14px !important; line-height: 1.5;'>Please Wait, do not refresh the page, we are trying to process the uploaded images and pull out faces from them, This will take a bit long.</p>
            </div>
        `,
        reverseButtons: false,
        showCancelButton: false,
        showConfirmButton: false,
        allowOutsideClick: false,
      });

      const request = await fetch(this.url, {
        method: "POST",
        body: formData,
        headers: {
          "User-Id": this.id,
        }
      });

      // Getting the necessary data
      sweetAlert1.close();

      // For Proper Visualizing
      const response = await request.json()
      console.log(response)
      const jsonData = response;
      // to display results in the 'results' div
      function displayResults() {
        resultsDiv.innerHTML = ""
        let successCounter = 0

        for (const [imageName, result] of Object.entries(jsonData)) {
          if (result?.error !== undefined) {
            resultsDiv.innerHTML += `<p class="alert alert-danger my-1">${result?.error} 😔</p>`;
            clearImages();
            break;
          }

          if (result?.criticalError !== undefined) {
            resultsDiv.innerHTML += `<p class="alert alert-danger my-1" title='Could not contact the face detection server, please try again'>'${imageName}' Image Error: An Error occured while trying to process this image, please try again 😔</p>`;
            continue
          }

          if (result.errors && result.errors.length > 0) {
            resultsDiv.innerHTML += `<p class="alert alert-danger my-1">Error for image ${imageName}': ${result.errors.join(", ")} 😔</p>`;
            clearImages()
          } else if (
            result.requestResults?.numberOfFacesDetected == 1
          ) {
            successCounter++;
            resultsDiv.innerHTML += `<p class="alert alert-success my-1">Success for image '${imageName}': ${result.requestResults.numberOfFacesDetected} face(s) detected 😀</p>`;
            clearImages()
          } else if (
            result.requestResults?.numberOfFacesDetected < 1
          ) {
            clearImages()
            resultsDiv.innerHTML += `<p class="alert alert-danger my-1">'${imageName}' Image Error: No faces detected 😔</p>`;
          } else if (
            result.requestResults?.numberOfFacesDetected > 1
          ) {
            resultsDiv.innerHTML += `<p class="text-danger my-1">'${imageName}' Image Error: Expected 1 face, but found ${result.requestResults.numberOfFacesDetected} Faces In the image 😔</p>`;
            clearImages()
          }
        }

        resultsDiv.innerHTML += `<div class="badge bg-light text-muted border my-1">${successCounter} Sucessfully Uploaded</div>`;
      }

      displayResults()
    };
  }

  resetWebcam() {
    this.capturedImagesFromWebCam = []
    document.getElementById('captured-images').innerHTML = ''
  }

  async sendWebCamImagesToBackend() {
    const resultsDiv = document.querySelector(".results");

    if (!this.capturedImagesFromWebCam.length) {
      alert('No captured images yet!')
      return; 
    }

    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-success",
        cancelButton: "btn btn-danger",
      },
      buttonsStyling: false,
    });

    const sweetAlert1 = swalWithBootstrapButtons.fire({
      title: false,
      html: `
            <div class='my-3'>
                <img src='../../assets/images/preloaders/Settings.gif' />
            </div>
            <div class='some-content' style='margin-bottom: -10px;'>
                <h4 class='mb-3'>Processing Images</h4>
                <p class='text-muted' style='font-size: 14px !important; line-height: 1.5;'>Please Wait, do not refresh the page, we are trying to process the uploaded images and pull out faces from them, This will take a bit long.</p>
            </div>
        `,
      reverseButtons: false,
      showCancelButton: false,
      showConfirmButton: false,
      allowOutsideClick: false,
    });

    const request = await fetch(this.url, {
      method: "POST",
      body: this.getUploadedImages(),
      headers: {
        "User-Id": this.id,
      }
    });

    // Getting the necessary data
    sweetAlert1.close();

    // For Proper Visualizing
    const response = await request.json()
    console.log(response)
    const jsonData = response;

    // Close modal and webcam after response
    document.getElementById('closeWebcamBtn').click()

    // Then scroll down to the errors 
    window.location.href = "#results"

    // to display results in the 'results' div
    resultsDiv.innerHTML = ""
    let successCounter = 0

    for (const [imageName, result] of Object.entries(jsonData)) {
      if (result?.error !== undefined) {
        resultsDiv.innerHTML += `<p class="alert alert-danger my-1">${result?.error} 😔</p>`;
        this.resetWebcam();
        break;
      }

      if (result?.criticalError !== undefined) {
        resultsDiv.innerHTML += `<p class="alert alert-danger my-1" title='Could not contact the face detection server, please try again'>'${imageName}' Image Error: An Error occured while trying to process this image, please try again 😔</p>`;
        continue
      }

      if (result.errors && result.errors.length > 0) {
        resultsDiv.innerHTML += `<p class="alert alert-danger my-1">Error for image ${imageName}': ${result.errors.join(", ")} 😔</p>`;
        this.resetWebcam()
      } else if (
        result.requestResults?.numberOfFacesDetected == 1
      ) {
        successCounter++;
        resultsDiv.innerHTML += `<p class="alert alert-success my-1">Success for image '${imageName}': ${result.requestResults.numberOfFacesDetected} face(s) detected 😀</p>`;
        this.resetWebcam()
      } else if (
        result.requestResults?.numberOfFacesDetected < 1
      ) {
        this.resetWebcam()
        resultsDiv.innerHTML += `<p class="alert alert-danger my-1">'${imageName}' Image Error: No faces detected 😔</p>`;
      } else if (
        result.requestResults?.numberOfFacesDetected > 1
      ) {
        resultsDiv.innerHTML += `<p class="text-danger my-1">'${imageName}' Image Error: Expected 1 face, but found ${result.requestResults.numberOfFacesDetected} Faces In the image 😔</p>`;
        this.resetWebcam()
      }
    }

    resultsDiv.innerHTML += `<div class="badge bg-light text-muted border my-1">${successCounter} Sucessfully Uploaded</div>`;
  }
}
