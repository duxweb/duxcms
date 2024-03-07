import React, { useRef } from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Link, Select, Form } from 'tdesign-react/esm'
import {
  PageTable,
  useSelect,
  TableRef,
  ButtonModal,
  DeleteButton,
  DeleteLink,
} from '@duxweb/dux-refine'

interface FileIconProps {
  mime: string
}
const FileIcon = ({ mime }: FileIconProps) => {
  switch (true) {
    case /^image\//.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
          <div className='i-tabler:photo h-6 w-6'></div>
        </div>
      )
    case /^video\//.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-success'>
          <div className='i-tabler:video h-6 w-6'></div>
        </div>
      )
    case /^audio\//.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-warning'>
          <div className='i-tabler:audio h-6 w-6'></div>
        </div>
      )
    case /^application\/pdf$/.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-error'>
          <div className='i-tabler:file-pdf h-6 w-6'></div>
        </div>
      )
    case /^application\/vnd\.openxmlformats-officedocument\.wordprocessingml\.document$/.test(mime):
    case /^application\/msword$/.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
          <div className='i-tabler:file-word h-6 w-6'></div>
        </div>
      )
    case /^application\/vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet$/.test(mime):
    case /^application\/vnd\.ms-excel$/.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
          <div className='i-tabler:file-excel h-6 w-6'></div>
        </div>
      )
    case /^application\/zip$/.test(mime):
    case /^application\/x-rar-compressed$/.test(mime):
    case /^application\/x-7z-compressed$/.test(mime):
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
          <div className='i-tabler:file-zip h-6 w-6'></div>
        </div>
      )
    default:
      return (
        <div className='h-10 w-10 flex items-center justify-center rounded p-2 text-white bg-brand'>
          <div className='i-tabler:file-unknown h-6 w-6'></div>
        </div>
      )
  }
}

const List = () => {
  const translate = useTranslate()
  const table = useRef<TableRef>(null)
  const { options, onSearch, queryResult } = useSelect({
    resource: 'tools.fileDir',
    optionLabel: 'name',
    optionValue: 'id',
  })

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'name',
        title: translate('tools.file.fields.name'),
        minWidth: 300,
        cell: ({ row }) => {
          return (
            <div className='flex items-center gap-2'>
              <div>
                <FileIcon mime={row.mime} />
              </div>
              <div className='flex flex-col'>
                <div>{row.name}</div>
                <div className='text-sm text-gray'>{row.mime}</div>
              </div>
            </div>
          )
        },
      },
      {
        colKey: 'dir_name',
        title: translate('tools.file.fields.dir'),
        width: 150,
      },
      {
        colKey: 'size',
        title: translate('tools.file.fields.size'),
        width: 150,
      },
      {
        colKey: 'driver',
        title: translate('tools.file.fields.driver'),
        width: 150,
      },
      {
        colKey: 'time',
        title: translate('tools.file.fields.time'),
        width: 200,
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 160,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Link theme='primary' href={row.url} target='_block'>
                {translate('buttons.show')}
              </Link>
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  const dirId = table.current?.filters?.dir_id

  return (
    <>
      <PageTable
        ref={table}
        columns={columns}
        table={{
          rowKey: 'id',
        }}
        filterRender={() => (
          <>
            <div>
              <ButtonModal
                resource='tools.fileDir'
                action='create'
                variant='outline'
                theme='default'
                title={translate('tools.file.fields.addDir')}
                icon={<div className='i-tabler:plus' />}
                component={() => import('./group')}
              >
                <></>
              </ButtonModal>
            </div>
            <Form.FormItem name='dir_id' initialData={options?.[0]?.value}>
              <Select
                filterable
                clearable
                onSearch={onSearch}
                options={options}
                placeholder={translate('tools.file.placeholder.dir')}
                loading={queryResult.isLoading}
              />
            </Form.FormItem>

            {table.current?.filters?.dir_id && (
              <div className='flex gap-2'>
                <ButtonModal
                  resource='tools.fileDir'
                  action='edit'
                  variant='outline'
                  theme='default'
                  title={translate('tools.file.fields.editDir')}
                  icon={<div className='i-tabler:edit' />}
                  component={() => import('./group')}
                  rowId={table.current?.filters.dir_id}
                >
                  <></>
                </ButtonModal>
                <DeleteButton
                  resource='tools.fileDir'
                  variant='outline'
                  theme='default'
                  icon={<div className='i-tabler:trash' />}
                  rowId={table.current?.filters.dir_id}
                >
                  <></>
                </DeleteButton>
              </div>
            )}
          </>
        )}
        actionRender={() => (
          <ButtonModal
            component={() => import('./upload')}
            action='upload'
            title={translate('tools.file.fields.upload')}
            icon={<div className='t-icon i-tabler:plus'></div>}
            rowId={dirId}
          />
        )}
      />
    </>
  )
}

export default List
