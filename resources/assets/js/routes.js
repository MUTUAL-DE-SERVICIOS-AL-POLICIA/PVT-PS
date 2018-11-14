import Home from './views/Welcome';
import Report from './views/Report';
import NegativeLoans from './views/NegativeLoans';
import Loans from './views/loans/loans';
import LoansSenasir from './views/loans/LoansSenasir';
import LoansInArrears from './views/loans/LoansInArrears';
import PartialDeaultLoans from './views/loans/PartialDeaultLoans';
import LoansCommand from './views/loans/LoanCommand';
import Amortization from './views/Amortization';
import OverdueLoans from './views/loans/OverdueLoans';
import TotalOverdueLoans from './views/loans/TotalOverdueLoans';
import Nav from './views/layout/Nav';
import Toolbar from './views/layout/Toolbar';
import Login from './views/user/Login';
import NewsSenasir from './views/loans/NewsSenasir';
export const routes = [
    {
        path: '/',
        name: 'home',
        components:{ 
            default: Home,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/report',
        name: 'report',
        components:{ 
            default: Report,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/negative_loans',
        name: 'negavitve_loans',
        components:{ 
            default: NegativeLoans,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/loans',
        name: 'loans',
        components:{ 
            default: Loans,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/loans_senasir',
        name: 'loans_senasir',
        components:{ 
            default: LoansSenasir,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/news_senasir',
        name: 'news_senasir',
        components:{ 
            default: NewsSenasir,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/partial_loans',
        name: 'partial_loans',
        components:{ 
            default: PartialDeaultLoans,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/loans_in_arriears',
        name: 'loans_in_arriears',
        components:{ 
            default: LoansInArrears,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/loans_command',
        name: 'loans_command',
        components:{ 
            default: LoansCommand,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/amortization',
        name: 'amortization',
        components:{ 
            default: Amortization,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/overdue_loans',
        name: 'overdue_loans',
        components:{ 
            default: OverdueLoans,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/total_overdue_loans',
        name: 'total_overdue_loans',
        components:{ 
            default: TotalOverdueLoans,
            toolbar: Toolbar,
            nav: Nav

        }
    },
    {
        path: '/login',
        name: 'login',
        components:{ 
            nomenu: Login,
        }
    },
];