import React from "react";
import { Link } from "react-router-dom";
import axios from "axios";

const Row = (props) => <div className="row">{props.children}</div>;
const Col = (props) => (
  <div className={"col-md-" + props.num + " col-sm-12 mb-3"}>
    {props.children}
  </div>
);

const Card = (props) => (
  <div className="card">
    {props.data.poster ? (
      <img
        className="card-img-top"
        src={props.data.poster}
        alt={props.data.title}
      />
    ) : (
      <div className="poster"></div>
    )}
    <div className="card-body">
      <h4 className="card-title">{props.data.title}</h4>
      <p className="card-text text-truncate">{props.data.description}</p>
    </div>
    <div className="card-footer overflow-hidden">
      <div className="float-left">
        <Link
          title="Edit gallery"
          to={`/${props.data.name}`}
          className="btn btn-sm btn-primary mr-2"
        >
          <i className="fa fa-edit text-light"></i>
        </Link>
        <a
          title="Preview gallery"
          className="btn btn-sm btn-info mr-2"
          target="_blank"
          href={`${site_url}/gallery/p/${props.data.name}`}
        >
          <i className="fa fa-eye text-light"></i>
        </a>
        <a
          title="Preview json gallery"
          className="btn btn-sm btn-warning"
          target="_blank"
          href={`${site_url}/api/gallery/${props.data.name}`}
        >
          <i className="fa fa-code text-light"></i>
        </a>
      </div>
      <div className="float-right">
        <button
          title="Delete gallery"
          className="btn btn-sm btn-danger"
          onClick={props.fn}
        >
          <i className="fa fa-trash"></i>
        </button>
      </div>
    </div>
  </div>
);

// Loader component
const Loader = () => (
  <div className="preloader">
    <div className="lds-ripple">
      <div></div>
      <div></div>
    </div>
  </div>
);

const UploadProgress = () => (
  <div className="file-progress">
    <span id="fileprogress"></span>
  </div>
);

const fetchData = async (url) => {
  const response = await axios.get(url);
  const result = await response.data;
  return result;
};

const postData = async (url, data, label, progress) => {
  const config = {
    onUploadProgress: function (progressEvent) {
      let percentCompleted = Math.round(
        (progressEvent.loaded * 100) / progressEvent.total
      );
      label.textContent = percentCompleted + "%";
      progress.style.width = percentCompleted + "%";
    },
  };
  const response = await axios.post(url, data, config);
  const result = await response.data;
  return result;
};

export { fetchData, postData, UploadProgress, Loader, Row, Col, Card };
