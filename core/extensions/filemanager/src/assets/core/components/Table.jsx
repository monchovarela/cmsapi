import React, { useState, useEffect } from "react";
import { useHistory } from "react-router-dom";
import { fetchData, renderIconType } from "./utils.jsx";


const Table = (props) => {

  let api_url = `${site_url}/api/filemanager/all`;
  let delete_url = `${site_url}/filemanager/delete`;

  const [data, setData] = useState([]);

  // watch data changes
  useEffect(() => setData(props.data),[props.data]);

  // delete file
  const deleteFile = (name) => {
    if(confirm('Are you sure to delete file?')){
      fetchData(delete_url + "/" + btoa(name)).then((r) => {
        message(r.message);
        if (r.status) fetchData(api_url).then((r) => setData(r));
      });
    }
  };

  let history = useHistory();
  const renameFile = (name,ext) => {
    history.push(`/rename/${name}/${ext}`);
  }

  return (
    <table className="table table-responsive-lg table-bordered mb-3">
      <thead>
        <tr>
          <th className="text-center">#</th>
          <th>Name</th>
          <th>Date</th>
          <th>Ext</th>
          <th>Size</th>
          <th className="text-center">Options</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item, index) => (
          <tr key={index}>
            <td className="text-center bg-light">{renderIconType(item.ext)}</td>
            <td>
              <a
                className="text-truncate mw-150 text-primary"
                target="_blank"
                href={item.url}
                title={item.name}
              >
                {item.name}
              </a>
            </td>
            <td>{item.other.date}</td>
            <td>{item.ext}</td>
            <td>{item.other.size}</td>
            <td className="text-center">
              <button
                onClick={() => renameFile(item.name,item.ext)}
                className="btn btn-link text-primary">
                <i className="fa fa-edit"></i>
              </button>
              <button
                onClick={() => deleteFile(item.url)}
                className="btn btn-link text-danger">
                <div className="fa fa-trash"></div>
              </button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Table;
