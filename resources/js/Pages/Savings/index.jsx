import AppLayout from "@/Layouts/AppLayout"

export default function Index(props) {
    return (
        <div>this is saving</div>
    )
}

Index.layout = (page) => <AppLayout title={page.props.pageSettings.title} children={page}/>