import React, { useState, useEffect } from 'react';
import Header from "../Header"
import { useHistory } from 'react-router-dom'
import axios from 'axios';
function Login() {
    const history = useHistory();
    useEffect(() => {
        if (localStorage.getItem('user-info'))
            history.push("/dashboard")
    }, [])
    const [username, setUsername] = useState("");
    const [password, setPassword] = useState("");
    const [errorMessage, setErrorMessage] = useState('');
    //const token = document.head.querySelector('meta[name="csrf-token"]');

    async function logIn() {
        axios.defaults.withCredentials = true;
        await axios.get('http://localhost:8000/sanctum/csrf-cookie').then(response => {
              axios.post('http://localhost:8000/api/login', { phone_or_email_or_username: username, password: password }).then(response => {
                console.info(response.data.success);
                if (response.data.success === true) {
                    if(response.data.data.token){
                        localStorage.setItem("ACCESS_TOKEN", JSON.stringify(response.data.data.token)) 
                    }
                    localStorage.setItem("user-info", JSON.stringify(response.data))
                    history.push('/dashboard')
                } else {
                    setErrorMessage('Please enter valid data!');
                }
            })

        });
    }
    return (
        <div className="col-sm-12" >
            <Header />
            <div className="col-sm-6 offset-sm-3">
                <h1>Login Page</h1>
                <input type="text" value={username} onChange={(e) => setUsername(e.target.value)} className="form-control" placeholder="Username" /><br />
                <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} className="form-control" placeholder="Password" /><br />
                {errorMessage && (<div className="error float-left"> {errorMessage} </div>)}<br />
                <button onClick={logIn} className="btn btn-primary">Log In</button>
            </div>
        </div >
    )
}
export default Login