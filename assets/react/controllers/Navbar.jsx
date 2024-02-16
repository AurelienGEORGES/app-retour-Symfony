import React from 'react';

export default function () {
    return <nav className="navbar navbar-expand-lg bg-body-tertiary">
        <div className="container-fluid m-2">
            <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span className="navbar-toggler-icon"></span>
            </button>
            <div className="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div className="navbar-nav">
                    <a className="nav-link mb-1 mt-2" href="/login"><span className="fw-normal fs-3">Connexion</span></a>
                    <a className="nav-link mb-1" href="/"><span className="fw-normal fs-3">Accueil</span></a>
                    <a className="nav-link my-1" href="/photo/bordereau"><span className="fw-normal fs-3">Photo bordereau</span></a>
                    <a className="nav-link my-1" href="/liste/bordereaux"><span className="fw-normal fs-3">Liste bordereaux</span></a>
                    <a className="nav-link my-1" href="/liste/attendus"><span className="fw-normal fs-3">Liste attendus ou sans attendus</span></a>
                    <a className="nav-link my-1" href="/produit"><span className="fw-normal fs-3">Ajouter produit libre</span></a>
                    <a className="nav-link my-1" href="/liste/receptionnes"><span className="fw-normal fs-3">Liste des réceptionnés</span></a>
                    <a className="nav-link my-1" href="/liste/stock"><span className="fw-normal fs-3">Liste stock</span></a>
                    <a className="nav-link mt-1 mb-2" href="/creation/palette"><span className="fw-normal fs-3">Création palette</span></a>
                </div>
            </div>
        </div>
    </nav>;
}
