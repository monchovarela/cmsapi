import React, { Fragment, useState, useEffect } from "react";
import { Link, useHistory } from "react-router-dom";

import axios from "axios";

import {
  fetchData,
  UploadProgress,
  postData,
  Loader,
  Row,
  Col,
} from "./utils.jsx";

const GalleryItem = ({ match }) => {
  // url to uploads
  let url = `${site_url}/api/gallery/${match.params.name}`;
  let upload_url = `${site_url}/gallery/upload/${match.params.name}`;
  let delete_url = `${site_url}/gallery/deleteimage`;
  let update_url = `${site_url}/gallery/update/${match.params.name}`;

  // initial data
  const [data, setData] = useState({});
  const [loaded, setLoaded] = useState(false);

  const [uid, setUid] = useState(undefined);
  const [name, setName] = useState(undefined);
  const [title, setTitle] = useState(undefined);
  const [description, setDescription] = useState(undefined);
  const [template, setTemplate] = useState(undefined);
  const [poster, setPoster] = useState(undefined);

  useEffect(() => {
    fetchData(url).then((r) => {
      let info = r.info[0];
      setLoaded(true);
      setData(r);

      setUid(info.uid);
      setName(info.name);
      setTitle(info.title);
      setDescription(info.description);
      setTemplate(info.template);
      setPoster(info.poster);
    });
  }, []);

  // update data
  const handleSubmit = (evt) => {
    evt.preventDefault();
    let update_data = {
      uid: uid,
      name: name,
      title: title,
      description: description,
      template: template,
      poster: poster,
    };
    postData(update_url, update_data, updateBtn, fileprogress).then((r) => {
      message(r.message);
      updateBtn.textContent = "Update";
      fileprogress.style.width = "0%";
    });
    return false;
  };

  // delete photos
  const handleDeletePhoto = (elem) => {
    fetchData(`${delete_url}/${btoa(elem)}`).then((r) => setData(r));
  };

  // Upload files
  const handleUpload = () => {
    let data = new FormData();
    data.append("file", fileElem.files[0]);
    postData(upload_url, data, filelabel, fileprogress)
      .then((res) => {
        if (res.status) {
          message(res.message);
          fetchData(url).then((r) => {
            setData(r);
            filelabel.textContent = "Upload image";
            fileprogress.style.width = "0%";
          });
        } else {
          filelabel.textContent = "Upload image";
          fileprogress.style.width = "0%";
          message("Error on upload file");
        }
      })
      .catch((errr) => message(err));
  };

  // replace poster
  const changePoster = (element) => {
    let old_url = `/public/galleries/${match.params.name}/small/`;
    let new_url = `/public/galleries/${match.params.name}/medium/`;
    element = element.replace(old_url,new_url);
    setPoster(site_url+element)                 
  }

  return loaded ? (
    <Fragment>
      <UploadProgress />
      <Row>
        <Col num="12">
          <div className="d-block mb-3 mt-2">
            <input
              className="d-none"
              type="file"
              id="fileElem"
              onChange={handleUpload}
            />
            <label
              id="filelabel"
              className="d-inline btn btn-outline-dark"
              htmlFor="fileElem">
              <i className="fa fa-image mr-2"></i>Upload image
            </label>
          </div>

          <div className="thumbnails bg-light">
            {data.images
              ? data.images.small.map((item, index) => (
                  <div className="thumbnail" key={index}>
                    <button
                      title="Delete photo"
                      className="btn btn-sm btn-danger rounded-0"
                      onClick={() => handleDeletePhoto(item)}>
                      <i className="fa fa-trash"></i>
                    </button>
                    <button
                      title="Add poster"
                      className="btn btn-sm btn-primary rounded-0"
                      onClick={() => changePoster(item)}>
                      <i className="fa fa-image"></i>
                    </button>
                    <img key={index} src={`${site_url}/${item}`} />
                  </div>
                ))
              : ""}
          </div>
        </Col>
      </Row>

      <form onSubmit={handleSubmit}>
        <Row>
          <Col num="6">
            <div className="form-group">
              <label>Title</label>
              <input
                type="text"
                name="title"
                className="form-control"
                value={title || ""}
                onChange={(e) => setTitle(e.target.value)}
              />
            </div>
            <div className="form-group">
              <label>Description</label>
              <textarea
                name="description"
                className="form-control"
                rows="5"
                value={description || ""}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>
          </Col>
          <Col num="6">
            <div className="form-group">
              <label>Poster</label>
              <input
                type="text"
                name="poster"
                className="form-control"
                value={poster || ""}
                onChange={(e) => setPoster(e.target.value)}
              />
            </div>
            <div className="form-group">
              <label>Template</label>
              <select
                name="template"
                className="form-control"
                onChange={(e) => setTemplate(e.target.value)}
              >
                <option value={template || "small"}>---</option>
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
              </select>
            </div>
          </Col>
        </Row>
        <Row>
          <Col num="6">
            <button
              type="submit"
              id="updateBtn"
              className="btn btn-sm btn-outline-primary rounded-0 mr-sm-3">
              Update
            </button>
            <Link to="/" className="btn btn-sm btn-outline-warning rounded-0">
              Cancel
            </Link>
          </Col>
        </Row>
      </form>
    </Fragment>
  ) : (
    <Loader />
  );
};

export default GalleryItem;
