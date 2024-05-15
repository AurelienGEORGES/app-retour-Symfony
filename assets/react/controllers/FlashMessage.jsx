import React from 'react'

const FlashMessage = (props) => {

    function removeMessage() {
        var eltMessage = document.querySelector('#message')
        eltMessage.remove()
    }

    return (
        <div id="message">
            <div className="flash-notice">
                <p className="text-success my-3 fs-3 mx-2 fw-normal text-center">{props.message}</p>
            </div>
            <div className='d-flex justify-content-center'>
                <button type="submit" className="btn btn-outline-success border-3 rounded-5" onClick={removeMessage}>
                    <span className='fs-3 fw-normal p-3'>OK!</span>
                </button>
            </div>
        </div>
    )
}

export default FlashMessage