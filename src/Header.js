import { Navbar, Nav, NavDropdown } from 'react-bootstrap'
import { useHistory } from 'react-router-dom'

function Header() {
    const history = useHistory();
    function logOut() {
        localStorage.clear();
        history.push("/login")
    }
    let user = JSON.parse(localStorage.getItem('user-info'))
    return (
            <Navbar bg="dark" variant="dark">
                <Navbar.Brand href="#home" className="align center">Airanswer</Navbar.Brand>
                <Nav className="mr-auto navbar_wrapper">
                    {
                        localStorage.getItem('user-info') ?
                            <>
                                {/* <Link to="/add">Add User</Link>
                                <Link to="/update">Update User</Link> */}
                            </>
                            :
                            <>

                            </>
                    }
                </Nav>
                {localStorage.getItem('user-info') ?
                    <Nav>
                        <NavDropdown title={user && user.name}>
                            <NavDropdown.Item onClick={logOut}> Logout </NavDropdown.Item>
                        </NavDropdown>
                    </Nav>
                    : null
                }
            </Navbar>
    )
}
export default Header
