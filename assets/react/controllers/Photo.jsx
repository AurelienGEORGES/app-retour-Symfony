import React from 'react'

const Photo = () => {

    async function openCamera() {
        const video = document.getElementById('video');

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.play();
        } catch (error) {
            console.error('Error accessing the camera: ', error);
        }
    }

    function closeCamera() {
        const video = document.getElementById('video');
        const stream = video.srcObject;
        const tracks = stream.getTracks();

        tracks.forEach(track => {
            track.stop();
        });

        video.srcObject = null;
    }

    async function takePhoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;

            video.onloadedmetadata = () => {
                video.play();
            };

            setTimeout(async () => {
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                const photo = canvas.toDataURL('image/png');

                // Insérer l'image capturée dans l'input file
                const photoInput = document.getElementById('photoInput');
                const blob = await dataURItoBlob(photo);
                const file = new File([blob], "photo_bordereau_" + Date.now() + ".png", { type: "image/png" });

                const fileList = new DataTransfer();
                fileList.items.add(file);

                photoInput.files = fileList.files;
                if (fileList.files.length > 0) { 
                    var imageUrl = URL.createObjectURL(fileList.files[0]);

                    var voirPhoto = document.querySelector('#photo-bordereau');

                    voirPhoto.src = imageUrl;
                }

                if (voirPhoto.src.trim() !== "") { 
                    voirPhoto.style.display = "block";
                }

                video.srcObject = stream;  
            }, 100); 
        } catch (error) {
            console.error('Error accessing the camera: ', error);
        }
    }

    // Convertir les données de l'URL en objet Blob
    function dataURItoBlob(dataURI) {
        return new Promise((resolve) => {
            const byteString = atob(dataURI.split(',')[1]);
            const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            const blob = new Blob([ab], { type: mimeString });
            resolve(blob);
        });
    }

    return (
        <div>
            <div className="my-3 d-flex justify-content-center">
                <button type="button" className="btn btn-outline-warning border-2 rounded-5" onClick={openCamera}>
                    <span className='fs-2 fw-normal'>Ouvrir caméra</span>
                </button>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <button type="button" className="btn btn-outline-warning border-2 rounded-5" onClick={closeCamera}>
                    <span className='fs-2 fw-normal'>Fermer caméra</span>
                </button>
            </div>
            <div className="d-flex justify-content-center my-3">
                <div className="scan border border-2 border-dark input-desktop m-1">
                    <video id="video" width="640" height="480"></video>
                    <canvas id="canvas" width="640" height="480" style={{ display: 'none' }}></canvas>
                </div>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <button type="button" className="btn btn-outline-warning border-2 rounded-5" onClick={takePhoto}>
                    <span className='fs-2 fw-normal'>Prendre photo</span>
                </button>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <img src="" alt="photo" id="photo-bordereau" style={{ display: 'none' }} />
            </div>
            <div className="d-flex justify-content-center my-3">
                <div className="m-1 border border-3 border-dark rounded-3">
                    <div className="input-group">
                        <input type="file" accept="image/*" capture="camera" id="photoInput" className="form-control m-0 input-desktop" name="photo" required />
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Photo