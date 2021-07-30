
import './App.css';
import { BrowserRouter, Route } from 'react-router-dom';
import Login from './Components/Login';
import Sidebar from './Components/Sidebar';
import Register from './Register';
import UpdateDevice from './Components/Devices/UpdateDevice';
//import AddProduct from './AddProduct';
import Protected from './Protected';
import Dashboard from './Components/Dashboard';
import Device from './Components/Devices/Device';
//import RegisterDevice from './Components/Devices/RegisterDevice';
import AddDevice from './Components/Devices/AddDevice';
import Faq from './Components/Faq/Faq';
import AddFaq from './Components/Faq/AddFaq';
import EditFaq from './Components/Faq/EditFaq';
function App() {
  return (
    <div className="App">
      <BrowserRouter>
       
        <Route path="/login">
          <Login />
        </Route>
        <Route path="/register">
          <Register />
        </Route>
        
        <Route path="/sidebar">
          <Protected Cmp={Sidebar}/>
        </Route>
        <Route path="/dashboard">
          <Protected Cmp={Dashboard}/>
        </Route>
        <Route path="/device">
          <Protected Cmp={Device}/>
        </Route>
         <Route path="/add-device">
          <Protected Cmp={AddDevice}/>
        </Route>
        <Route path="/edit-device/:id">
          <Protected Cmp={UpdateDevice}/>
        </Route>
        {/* <Route path="/add-device">
          <Protected Cmp={RegisterDevice}/>
        </Route> */}
        <Route path="/faq">
          <Protected Cmp={Faq}/>
        </Route>
        <Route path="/add-faq">
          <Protected Cmp={AddFaq}/>
        </Route>
        <Route path="/edit-faq/:id">
          <Protected Cmp={EditFaq}/>
        </Route>
      </BrowserRouter>
    </div>
  );
}

export default App;
