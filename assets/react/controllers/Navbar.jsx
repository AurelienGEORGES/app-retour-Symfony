import React from 'react';

export default function () {
    return <nav className="navbar navbar-expand-lg bg-body-tertiary">
        <div className="container-fluid m-2">
            <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span className="navbar-toggler-icon"></span>
            </button>
            <div className="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div className="navbar-nav">
                    <a className="nav-link mt-2 m-md-0" href="/login"><span className="fw-normal fs-5">Connexion</span></a>
                    <a className="nav-link my-1 m-md-0" href="/"><span className="fw-normal fs-5">Accueil</span></a>
                    <a className="nav-link my-1 m-md-0" href="/photo/bordereau"><span className="fw-normal fs-5">Bordereau</span></a>
                    <a className="nav-link my-1 m-md-0" href="/liste/bordereaux"><span className="fw-normal fs-5">Liste bordereaux</span></a>
                    <a className="nav-link my-1 m-md-0" href="/liste/attendus"><span className="fw-normal fs-5">Liste attendus</span></a>
                    <a className="nav-link my-1 m-md-0" href="/produit"><span className="fw-normal fs-5">Produit libre</span></a>
                    <a className="nav-link my-1 m-md-0" href="/liste/receptionnes"><span className="fw-normal fs-5">Liste réceptionnés</span></a>
                    <a className="nav-link my-1 m-md-0" href="/liste/stock"><span className="fw-normal fs-5">Stock</span></a>
                    <a className="nav-link my-1 m-md-0" href="/creation/palette"><span className="fw-normal fs-5">Création palette</span></a>
                    <a className="nav-link mb-2 m-md-0" href="/liste/palettes"><span className="fw-normal fs-5">Liste palettes</span></a>
                </div>
            </div>
        </div>
    </nav>;
}
