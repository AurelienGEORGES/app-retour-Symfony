import React from 'react'

const Photo = (props) => {

    async function openCamera(nbVideo) {
        const video = document.getElementById('video'+ nbVideo);

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            video.play();
        } catch (error) {
            console.error('Error accessing the camera: ', error);
        }
    }

    function closeCamera(nbClose) {
        const video = document.getElementById('video' + nbClose);
        const stream = video.srcObject;
        const tracks = stream.getTracks();

        tracks.forEach(track => {
            track.stop();
        });

        video.srcObject = null;
    }

    async function takePhoto(nbPhoto) {
        const video = document.getElementById('video' + nbPhoto);
        const canvas = document.getElementById('canvas' + nbPhoto);
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
                const photoInput = document.getElementById('photoInput' + nbPhoto);
                const blob = await dataURItoBlob(photo);
                const file = new File([blob], "photo_bordereau_" + Date.now() + ".png", { type: "image/png" });

                const fileList = new DataTransfer();
                fileList.items.add(file);

                photoInput.files = fileList.files;
                if (fileList.files.length > 0) { 
                    var imageUrl = URL.createObjectURL(fileList.files[0]);

                    var voirPhoto = document.querySelector('#photoBordereau' + nbPhoto);

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
                <button type="button" className="btn btn-outline-primary border-2 rounded-5" onClick={() => openCamera(props.nbVideo)}>
                    <span className='fs-2 fw-normal'>Ouvrir caméra</span>
                </button>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <button type="button" className="btn btn-outline-primary border-2 rounded-5" onClick={() => closeCamera(props.nbClose)}>
                    <span className='fs-2 fw-normal'>Fermer caméra</span>
                </button>
            </div>
            <div className="d-flex justify-content-center my-3">
                <div className="scan border border-2 border-primary input-desktop m-1">
                    <video id={props.video} width="640" height="480"></video>
                    <canvas id={props.canvas} width="640" height="480" style={{ display: 'none' }}></canvas>
                </div>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <button type="button" className="btn btn-outline-primary border-2 rounded-5" onClick={() => takePhoto(props.nbPhoto)}>
                    <span className='fs-2 fw-normal'>Prendre photo</span>
                </button>
            </div>
            <div className="my-3 d-flex justify-content-center">
                <img src="" alt="photo" id={props.photoBordereau} style={{ display: 'none' }} />
            </div>
            <div className="d-flex justify-content-center my-3">
                <div className="m-1 border border-3 border-primary rounded-3">
                    <div className="input-group">
                        <input type="file" accept="image/*" capture="camera" id={props.photoInput} className="form-control m-0 input-desktop" name={props.photo} required />
                    </div>
                </div>
            </div>
        </div>
    )
}

export default Photo