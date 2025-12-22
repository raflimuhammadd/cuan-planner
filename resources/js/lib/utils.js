import { router } from '@inertiajs/react';
import { clsx } from 'clsx';
import { format, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';
import { toast } from 'sonner';
import { twMerge } from 'tailwind-merge';

function cn(...inputs) {
    return twMerge(clsx(inputs));
}

function flashMessage(params) {
    return params.props.flashMessage;
}

const deleteAction = (url, { closeModal, ...options } = {}) => {
    const defaultOptions = {
        preserveScroll: true,
        preserveState: true,

        onSuccess: (success) => {
            const flash = flashMessage(success);

            if (flash) {
                toast[flash.type](flash.message);
            }

            if (closeModal && typeof closeModal === 'function') {
                closeModal();
            }
        },

        ...options,
    };

    router.delete(url, defaultOptions);
};

// format ke jam indo
const formatDateIndo = (dateString) => {
    if (!dateString) return '-';

    return format(parseISO(dateString), 'eeee, dd MMMM yyyy', {
        locale: id,
    });
};

// convert ke rupiah
const formatToRupiah = (amount) => {
    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    });

    return formatter.format(amount);
};

// objek budget type
const BUDGETTYPE = {
    INCOME: 'Penghasilan',
    SAVING: 'Tabungan dan Investasi',
    DEBT: 'Cicilan Hutang',
    BILL: 'Tagihan',
    SHOPPING: 'Belanja',
};

const BUDGETTYPEVARIANT = {
    [BUDGETTYPE.INCOME]: 'emerald',
    [BUDGETTYPE.SAVING]: 'orange',
    [BUDGETTYPE.DEBT]: 'red',
    [BUDGETTYPE.BILL]: 'sky',
    [BUDGETTYPE.SHOPPING]: 'purple',
};

// objek month type
const MONTHTYPE = {
    JANUARI: 'Januari',
    FEBRUARI: 'Februari',
    MARET: 'Maret',
    APRIL: 'April',
    MEI: 'Mei',
    JUNI: 'Juni',
    JULI: 'Juli',
    AGUSTUS: 'Agustus',
    SEPTEMBER: 'September',
    OKTOBER: 'Oktober',
    NOVEMBER: 'November',
    DESEMBER: 'Desember',
};

const MONTHTYPEVARIANT = {
    [MONTHTYPE.JANUARI]: 'fuchsia',
    [MONTHTYPE.FEBRUARI]: 'orange',
    [MONTHTYPE.MARET]: 'emerald',
    [MONTHTYPE.APRIL]: 'sky',
    [MONTHTYPE.MEI]: 'purple',
    [MONTHTYPE.JUNI]: 'rose',
    [MONTHTYPE.JULI]: 'pink',
    [MONTHTYPE.AGUSTUS]: 'red',
    [MONTHTYPE.SEPTEMBER]: 'violet',
    [MONTHTYPE.OKTOBER]: 'blue',
    [MONTHTYPE.NOVEMBER]: 'lime',
    [MONTHTYPE.DESEMBER]: 'teal',
};

// objek asset type
const ASSETTYPE = {
    CASH: 'Kas',
    PERSONAL: 'Personal',
    SHORTTERM: 'Investasi Jangka Pendek',
    MIDTERM: 'Investasi Jangka Menengah',
    LONGTERM: 'Investasi Jangka Panjang',
};

const ASSETTYPEVARIANT = {
    [ASSETTYPE.CASH]: 'emerald',
    [ASSETTYPE.PERSONAL]: 'orange',
    [ASSETTYPE.SHORTTERM]: 'red',
    [ASSETTYPE.MIDTERM]: 'sky',
    [ASSETTYPE.LONGTERM]: 'purple',
};

// objek liability type
const LIABILITYTPE = {
    SHORTTERMDEBT: 'Hutang Jangka Pendek',
    MIDTERMDEBT: 'Hutang Jangka Menengah',
    LONGTERMDEBT: 'Hutang Jangka Panjang',
};

const LIABILITYTPEVARIANT = {
    [LIABILITYTPE.SHORTTERMDEBT]: 'emerald',
    [LIABILITYTPE.MIDTERMDEBT]: 'orange',
    [LIABILITYTPE.LONGTERMDEBT]: 'red',
};

const LIABILITYDESCRIPTION = {
    [LIABILITYTPE.SHORTTERMDEBT]: 'Tenor 1-5 Tahun',
    [LIABILITYTPE.MIDTERMDEBT]: 'Tenor 5-10 Tahun',
    [LIABILITYTPE.LONGTERMDEBT]: 'Tenor > 10 Tahun',
};

const messages = {
    503: {
        title: 'Service Unavailable',
        description: 'Maaf sedang maintenance. Silahkan cek berkala...',
        status: 503,
    },
    500: {
        title: 'Server Error',
        description: 'Oops, ada yang salah...',
        status: 500,
    },
    404: {
        title: 'Not Found',
        description: 'Maaf laman yang anda cari tidak ditemukan...',
        status: 404,
    },
    403: {
        title: 'Forbidden',
        description: 'Anda dilarang mengakses laman ini...',
        status: 403,
    },
    401: {
        title: 'Unauthorized',
        description: 'Anda tidak memiliki akses pada laman ini...',
        status: 401,
    },
    429: {
        title: 'To Many Request',
        description: 'Segera ulangi lagi dalam beberapa saat...',
        status: 429,
    },
};

export {
    ASSETTYPE,
    ASSETTYPEVARIANT,
    BUDGETTYPE,
    BUDGETTYPEVARIANT,
    cn,
    deleteAction,
    flashMessage,
    formatDateIndo,
    formatToRupiah,
    LIABILITYDESCRIPTION,
    LIABILITYTPE,
    LIABILITYTPEVARIANT,
    messages,
    MONTHTYPE,
    MONTHTYPEVARIANT,
};
