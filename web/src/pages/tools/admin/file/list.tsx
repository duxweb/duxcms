import React, { useRef } from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Link } from 'tdesign-react/esm'
import {
  PageTable,
  TableRef,
  ButtonModal,
  DeleteLink,
  FilterSider,
  FileIcon,
} from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const table = useRef<TableRef>(null)

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
        siderRender={() => (
          <FilterSider
            title={translate('tools.file.fields.dir')}
            component={() => import('./group')}
            resource='tools.fileDir'
            field='dir_id'
            optionLabel='name'
            optionValue='id'
          />
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
