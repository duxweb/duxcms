import React, { useRef } from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Button, Link, Popconfirm, Select, Form } from 'tdesign-react/esm'
import { PageTable, Modal, useSelect, TableRef } from '@duxweb/dux-refine'

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
  const { mutate } = useDelete()
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
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'tools.file',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
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
              <Modal
                title={translate('tools.file.fields.addDir')}
                trigger={
                  <Button icon={<div className='i-tabler:plus' />} variant='outline'></Button>
                }
                component={() => import('./group')}
              ></Modal>
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
                <Modal
                  title={translate('tools.file.fields.editDir')}
                  trigger={
                    <Button icon={<div className='i-tabler:edit' />} variant='outline'></Button>
                  }
                  component={() => import('./group')}
                  componentProps={{
                    id: table.current?.filters.dir_id,
                  }}
                ></Modal>
                <Popconfirm
                  content={translate('buttons.confirm')}
                  destroyOnClose
                  placement='top'
                  showArrow
                  theme='default'
                  onConfirm={() => {
                    mutate({
                      resource: 'tools.file',
                      id: table.current?.filters.dir_id,
                    })
                  }}
                >
                  <Button icon={<div className='i-tabler:trash' />} variant='outline'></Button>
                </Popconfirm>
              </div>
            )}
          </>
        )}
        actionRender={() => (
          <>
            <Modal
              title={translate('tools.file.fields.upload')}
              trigger={
                <Button icon={<div className='i-tabler:plus t-icon'></div>}>
                  {translate('tools.file.fields.upload')}
                </Button>
              }
              component={() => import('./upload')}
              componentProps={{
                id: dirId,
              }}
            ></Modal>
          </>
        )}
      />
    </>
  )
}

export default List
